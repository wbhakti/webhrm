<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function logout(Request $request)
    {
        // Menghapus semua data dari sesi
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function postlogin(Request $request)
    {
        try {

            $passHash = base64_encode(hash_hmac('sha256', $request->input('username') . ':' . $request->input('password'), '#@R4dJaAN91n?#@', true));

            // Cek apakah username dan password cocok
            $user = DB::table('karyawan')
                ->where('email', $request->input('username'))
                ->where('password', $passHash)
                ->first();

            if ($user) {
                // Redirect ke halaman dashboard atau halaman lainnya
                session(['user_id' => $request->input('username'), 'role' => $user->ROLE, 'id' => $user->ID_KARYAWAN, 'nama' => $user->NAMA, 'outlet' => $user->OUTLET]);
                return redirect()->route('dashboard');
            } else {
                // Jika username tidak ditemukan atau password salah, kembali ke halaman login dengan pesan error
                return back()->with('error', 'Username atau password salah');
            }
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server');
        }
    }

    public function ListEmploye(Request $request)
    {
        try {

            $query = DB::table('karyawan')
                ->join('bagian', 'karyawan.BAGIAN', '=', 'bagian.ID_BAGIAN') // Melakukan join
                ->join('jabatan', 'karyawan.JABATAN', '=', 'jabatan.ID_JABATAN')
                ->leftJoin('outlet', 'karyawan.OUTLET', '=', 'outlet.ID_OUTLET')
                ->select(
                    'karyawan.*',
                    'bagian.BAGIAN as nama_bagian',
                    'jabatan.NAMA_JABATAN as nama_jabatan',
                    'outlet.NAMA as nama_toko'
                );
            $query->where('karyawan.is_delete', false);

            if (session('role') == "2" || session('role') == "3") {
                //admin data dan admin hrd hanya bisa ambil data role user
                $query->where('karyawan.ROLE', '4');
            }

            //filter bagian
            $filter = $request->input('filterBagian');
            if (!empty($filter)) {

                if ($filter == "headoffice") {
                    $query->where('bagian.ID_BAGIAN', '1');
                } else if ($filter == "gudang") {
                    $query->where('bagian.ID_BAGIAN', '2');
                } else if ($filter == "outlet") {
                    $query->where('bagian.ID_BAGIAN', '3');

                    //filter outlet
                    $query->where('outlet.ID_OUTLET', $request->input('filterOutlet'));
                }
            }

            $listKaryawan = $query->get();
            
            foreach ($listKaryawan as $karyawan) {
                $karyawan->formatted_tanggal_lahir = Carbon::parse($karyawan->TANGGAL_LAHIR)->format('d F Y');
                $karyawan->formatted_tanggal_gabung = Carbon::parse($karyawan->TANGGAL_BERGABUNG)->format('d F Y');
                $karyawan->formatted_awal_kontrak = Carbon::parse($karyawan->AWAL_KONTRAK)->format('d F Y');
                $karyawan->formatted_akhir_kontrak = Carbon::parse($karyawan->AKHIR_KONTRAK)->format('d F Y');
                $karyawan->formatted_tanggal_lahir_edit = Carbon::parse($karyawan->TANGGAL_LAHIR)->format('Y-m-d');
                $karyawan->formatted_tanggal_gabung_edit = Carbon::parse($karyawan->TANGGAL_BERGABUNG)->format('Y-m-d');
                $karyawan->formatted_awal_kontrak_edit = Carbon::parse($karyawan->AWAL_KONTRAK)->format('Y-m-d');
                $karyawan->formatted_akhir_kontrak_edit = Carbon::parse($karyawan->AKHIR_KONTRAK)->format('Y-m-d');
            }

            //ambil data outlet
            $listOutlet = DB::table('outlet')->get();

            // Mengirim data ke tampilan
            return view('listkaryawan', [
                'listData' => $listKaryawan,
                'listOutlet' => $listOutlet
            ]);
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('listkaryawan', ['error' => 'Terjadi kesalahan : ' . $e->getMessage()]);
        }
    }

    public function AddEmploye()
    {
        try {

            $listOutlet = DB::table('outlet')->get();

            if ($listOutlet->isEmpty()) {
                return redirect('/dashboard')->with('error', 'Tidak ada data outlet yang ditemukan.');
            } else {

                if (session('role') == "3") {
                    //admin HRD hanya bisa add role user saja
                    $listRole = DB::table('role')->where('ID_ROLE', '4')->get();
                } else {
                    $listRole = DB::table('role')->get();
                }

                return view('addkaryawan', [
                    'data' => $listOutlet,
                    'dataRole' => $listRole
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Terjadi kesalahan ambil data outlet');
        }
    }

    public function PostEmploye(Request $request)
    {
        try {

            $pass = $this->generateRandomPassword();
            $passHash = base64_encode(hash_hmac('sha256', $request->input('email') . ':' . $pass, '#@R4dJaAN91n?#@', true));
            $nik = $this->generateNik($request->input('jabatan'));

            if ($request->input('status_karyawan') == "TETAP") {
                $masaKontrak = "TETAP";
                $akhirKontrak = null;
            } else {
                $masaKontrak = $request->input('kontrak') . " Bulan";
                $akhirKontrak = Carbon::parse($request->input('tanggal_bergabung'))->addMonth($request->input('kontrak'))->toDateString();
            }

            if ($request->input('bagian') == "3") {
                $dataOutlet = $request->input('outlet');
            } else {
                $dataOutlet = null;
            }

            DB::table('karyawan')->insert([
                'NIK' => $request->input('ktp'),
                'NAMA' => $request->input('nama'),
                'TEMPAT_LAHIR' => $request->input('tempat_lahir'),
                'TANGGAL_LAHIR' => $request->input('tanggal_lahir'),
                'NO_HP' => $request->input('no_hp'),
                'EMAIL' => $request->input('email'),
                'ALAMAT_KTP' => $request->input('alamat_ktp'),
                'ALAMAT_TINGGAL' => $request->input('alamat_tinggal'),
                'TANGGAL_BERGABUNG' => $request->input('tanggal_bergabung'),
                'BAGIAN' => $request->input('bagian'),
                'JABATAN' => $request->input('jabatan'),
                'OUTLET' => $dataOutlet,
                'STATUS_KARYAWAN' => $request->input('status_karyawan'),
                'MASA_KONTRAK' => $masaKontrak,
                'AWAL_KONTRAK' => $request->input('tanggal_bergabung'),
                'AKHIR_KONTRAK' => $akhirKontrak,
                'SALDO_CUTI' => '12',
                'PASSWORD' => $passHash,
                'ROLE' => $request->input('role'),
                'nomor_karyawan' => $nik
            ]);

            //auto kirim email konfirmasi, dan password
            $this->sendEmailRegistration($request, $pass, $request->input('ktp'));

            return redirect('/dashboard/list-karyawan')->with('success', 'Berhasil simpan data karyawan');
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan input data');
        }
    }

    public function EditEmploye(Request $request)
    {
        try {

            if ($request->input('status_karyawan') == "TETAP") {
                $masaKontrak = "TETAP";
                $akhirKontrak = null;
            } else {
                $masaKontrak = $request->input('masa_kontrak') . " Bulan";
                $akhirKontrak = $request->input('akhir_kontrak');
            }

            if ($request->input('bagian') == "3") {
                $dataOutlet = $request->input('outlet');
            } else {
                $dataOutlet = null;
            }

            // Update data karyawan
            DB::table('karyawan')->where('id_karyawan', $request->input('idkaryawan'))->update([
                'NIK' => $request->input('nik'),
                'NAMA' => $request->input('nama_karyawan'),
                'TEMPAT_LAHIR' => $request->input('tempat_lahir'),
                'TANGGAL_LAHIR' => $request->input('tanggal_lahir'),
                'NO_HP' => $request->input('nomor_hp'),
                'EMAIL' => $request->input('email'),
                'ALAMAT_KTP' => $request->input('alamat_ktp'),
                'ALAMAT_TINGGAL' => $request->input('alamat_tinggal'),
                'BAGIAN' => $request->input('bagian'),
                'JABATAN' => $request->input('jabatan'),
                'OUTLET' => $dataOutlet,
                'STATUS_KARYAWAN' => $request->input('status_karyawan'),
                'MASA_KONTRAK' => $masaKontrak,
                'AWAL_KONTRAK' => $request->input('awal_kontrak'),
                'AKHIR_KONTRAK' => $akhirKontrak,
                'SALDO_CUTI' => $request->input('saldo_cuti')
            ]);

            // Update berhasil
            return redirect('/dashboard/list-karyawan')->with('success', 'Berhasil update data karyawan');
        } catch (\Exception $e) {
            Log::error('Error occurred during update: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat update data karyawan');
        }
    }

    public function DeleteEmploye(Request $request)
    {
        try {
            //dd($request);

            // delete data karyawan
            DB::table('karyawan')->where('id_karyawan', $request->input('idkaryawan'))->update([
                'is_delete' => true
            ]);

            // delete berhasil
            return redirect('/dashboard/list-karyawan')->with('success', 'Berhasil delete data karyawan');
        } catch (\Exception $e) {
            Log::error('Error occurred during update: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat delete data karyawan');
        }
    }

    public function ChangePassword()
    {
        return view('changepassword');
    }

    public function PostChangePassword(Request $request)
    {
        try {

            $passHash = base64_encode(hash_hmac('sha256', session('user_id') . ':' . $request->input('new_password'), '#@R4dJaAN91n?#@', true));

            //update password
            DB::table('karyawan')
                ->where('id_karyawan', session('id'))
                ->update([
                    'password' => $passHash,
                ]);

            $request->session()->flush();

            return redirect('/login')->with('success', 'Berhasil ubah password, silahkan login ulang!');
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server');
        }
    }

    public function ResetPassword()
    {
        return view('resetpassword');
    }

    public function PostResetPassword(Request $request)
    {
        try {

            $pass = $this->generateRandomPassword();
            $passHash = base64_encode(hash_hmac('sha256', $request->input('email') . ':' . $pass, '#@R4dJaAN91n?#@', true));

            //cek user
            $user = DB::table('karyawan')
                ->where('email', $request->input('email'))
                ->where('nik', $request->input('nik'))
                ->first();

            if (empty($user)) {
                return back()->with('error', 'Email atau NIK tidak terdaftar!');
            } else {

                //update password
                DB::table('karyawan')
                    ->where('email', $request->input('email'))
                    ->where('nik', $request->input('nik'))
                    ->update([
                        'password' => $passHash,
                    ]);

                //auto kirim email konfirmasi, dan password
                $this->SendEmailResetPassword($user->NAMA, $request->input('email'), $request->input('nik'), $pass);

                return redirect('/login')->with('success', 'Reset password berhasil, silahkan cek Email untuk info password terbaru.');
            }
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server');
        }
    }

    public function SendEmailRegistration(Request $request, $password, $nik)
    {
        try {

            //kirim Email
            $data = [
                'nama_lengkap' => $request->input('nama'),
                'email' => $request->input('email'),
                'nik' => $nik,
                'password' => $password,
            ];

            // Konten HTML untuk email
            $bodyEmail = '
                <html>
                <body>
                    <h1 style="color: #3490dc;">Anda sudah terdaftar sebagai karyawan, ' . $data['nama_lengkap'] . '</h1>
                    <p>Berikut adalah informasi Login Anda:</p>
                    
                    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
                        <tr>
                            <th style="background-color: #f2f2f2; text-align: left;">Informasi</th>
                            <th style="background-color: #f2f2f2; text-align: left;">Detail</th>
                        </tr>
                        <tr>
                            <td>Nama Karyawan</td>
                            <td>' . $data['nama_lengkap'] . '</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>' . $data['email'] . '</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>' . $data['nik'] . '</td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>' . $data['password'] . '</td>
                        </tr>
                    </table>
            
                    <br>
                    <p>Segara lakukan perubahan password!. <br>Hubungi Admin jika Anda memiliki masalah dengan Login.</p>
                    <p><strong>Admin HRM</strong></p>
                </body>
                </html>
            ';

            Mail::html($bodyEmail, function ($message) use ($data) {
                $message->to($data['email'], $data['nama_lengkap']);
                $message->subject('Informasi Login Karyawan');
            });
        } catch (\Exception $e) {
            Log::error('Error occurred : ' . $e->getMessage());
        }
    }

    public function SendEmailResetPassword($nama, $email, $nik, $password)
    {
        try {

            //kirim Email
            $data = [
                'nama_lengkap' => $nama,
                'email' => $email,
                'nik' => $nik,
                'password' => $password,
            ];

            // Konten HTML untuk email
            $bodyEmail = '
                <html>
                <body>
                    <h1 style="color: #3490dc;">Reset password berhasil ' . $data['nama_lengkap'] . '</h1>
                    <p>Berikut adalah informasi Login Anda:</p>
                    
                    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
                        <tr>
                            <th style="background-color: #f2f2f2; text-align: left;">Informasi</th>
                            <th style="background-color: #f2f2f2; text-align: left;">Detail</th>
                        </tr>
                        <tr>
                            <td>Nama Karyawan</td>
                            <td>' . $data['nama_lengkap'] . '</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>' . $data['email'] . '</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>' . $data['nik'] . '</td>
                        </tr>
                        <tr>
                            <td>Password baru</td>
                            <td>' . $data['password'] . '</td>
                        </tr>
                    </table>
            
                    <br>
                    <p>Segara lakukan perubahan password!. <br>Hubungi Admin jika Anda memiliki masalah dengan Login.</p>
                    <p><strong>Admin HRM</strong></p>
                </body>
                </html>
            ';

            Mail::html($bodyEmail, function ($message) use ($data) {
                $message->to($data['email'], $data['nama_lengkap']);
                $message->subject('Informasi Login Karyawan');
            });
        } catch (\Exception $e) {
            Log::error('Error occurred : ' . $e->getMessage());
        }
    }

    function generateRandomPassword()
    {
        // Karakter yang akan digunakan dalam password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$&_';

        // Mengacak karakter dan menghasilkan password acak
        $randomPassword = '';
        for ($i = 0; $i < 6; $i++) {
            $randomPassword .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomPassword;
    }
    public function generateNik($fixedPart)
    {
        // Mendapatkan tanggal saat ini dalam format yyyyMM
        $datePart = date("Ym");
        $incrementPart = 1; // mulai dari 001

        // Membuat NIK dasar
        $nikBase = $datePart . $fixedPart;

        // Memeriksa apakah NIK sudah ada di database
        do {
            // Membuat NIK lengkap dengan bagian increment
            $nik = $nikBase . str_pad($incrementPart, 3, '0', STR_PAD_LEFT);

            // Mencari apakah NIK sudah ada di database
            $exists = DB::table('karyawan')->where('nomor_karyawan', $nik)->exists();

            // Jika NIK ada, increment angka
            if ($exists) {
                $incrementPart++;
            }
        } while ($exists); // Ulangi hingga NIK yang unik ditemukan

        // Sekarang $nik berisi NIK yang unik
        return $nik;
    }

    public function getJabatan($id)
    {
        try {

            $listJabatan = DB::table('jabatan')
                ->where('id_bagian', $id)
                ->get();

            return response()->json($listJabatan);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }

}
