<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CutiController extends Controller
{
    public function ApprovalCuti(Request $request)
    {
        try{
            
            // Ambil nilai status dari request
            $filter = $request->input('status');
            if($filter == "pending"){
                $statusFilter = "1";
            }
            else if($filter == "approve"){
                $statusFilter = "2";
            }
            else if($filter == "reject"){
                $statusFilter = "3";
            }else{
                $statusFilter = $filter;
            }

            // Buat query dasar
            $query = DB::table('history_cuti')
            ->join('karyawan', 'karyawan.id_karyawan', '=', 'history_cuti.id_karyawan')
            ->leftJoin('outlet', 'karyawan.outlet', '=', 'outlet.id_outlet')
            ->join('status_cuti', 'status_cuti.id_status_cuti', '=', 'history_cuti.status')
            ->select(
                'history_cuti.*', 
                'outlet.nama as nama_outlet',
                'karyawan.NAMA as nama_karyawan',
                'status_cuti.status as status_cuti'
            );

            // Jika ada filter status, tambahkan kondisi where
            if (empty($statusFilter)) {
                $query->where('history_cuti.status', '1');
            }else{
                $query->where('history_cuti.status', $statusFilter);
            }

            // Eksekusi query untuk mengambil data
            $listCuti = $query->get();

            return view('approvalcuti', [
                'listData' => $listCuti
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Terjadi kesalahan ambil data cuti');
        }
    }

    public function PostApprovalCuti(Request $request)
    {
        try {

            if ($request->input('action') === 'approve') {

                //ambil data user
                $user = DB::table('karyawan')
                ->join('history_cuti', 'history_cuti.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('id_cuti', $request->input('idcuti'))
                ->select(
                    'karyawan.*', 
                    'history_cuti.*'
                )
                ->first();

                $sisaCuti = $user->SALDO_CUTI - $user->JUMLAH_HARI;

                //update status cuti
                DB::table('history_cuti')
                ->where('id_cuti', $request->input('idcuti'))
                ->update([
                    'status' => '2',
                ]);

                //update jumlah cuti
                DB::table('karyawan')
                ->where('id_karyawan', $user->ID_KARYAWAN)
                ->update([
                    'saldo_cuti' => $sisaCuti,
                ]);

                //kirim email
                $this->SendEmailCuti($user->NAMA,$user->EMAIL,$user->NIK, 'DITERIMA',$user->JUMLAH_HARI,$user->TANGGAL_AWAL,$user->TANGGAL_AKHIR);

                return redirect('/dashboard/approval-cuti')->with('success', 'Berhasil approved cuti');
            } elseif ($request->input('action') === 'reject') {

                //ambil data user
                $user = DB::table('karyawan')
                ->join('history_cuti', 'history_cuti.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('id_cuti', $request->input('idcuti'))
                ->select(
                    'karyawan.*', 
                    'history_cuti.*'
                )
                ->first();

                // Logika untuk menolak cuti
                DB::table('history_cuti')
                ->where('id_cuti', $request->input('idcuti'))
                ->update([
                    'status' => '3',
                    'keterangan' => $request->input('reason'),
                ]);

                //kirim email
                $this->SendEmailCuti($user->NAMA,$user->EMAIL,$user->NIK, 'DITOLAK',$user->JUMLAH_HARI,$user->TANGGAL_AWAL,$user->TANGGAL_AKHIR);

                return redirect('/dashboard/approval-cuti')->with('success', 'Berhasil tolak cuti');
            }else{
                return redirect('/dashboard/approval-cuti')->with('error', 'Gagal proses data cuti');
            }
            
        } catch (\Exception $e) {
            //dd($e);
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan server');
        }
    }

    public function AddCuti()
    {
        try{
            
            $user = DB::table('karyawan')
            ->where('email', session('user_id'))
            ->first();

            if ($user) {

                $historyCutiPending = DB::table('history_cuti')
                ->where('id_karyawan', session('id'))
                ->where('status', '1') // cek jika status 'pending'
                ->exists();

                if ($historyCutiPending) {
                    return view('addcuti', ['user' => $user, 'pending' => 'Y']);
                } else {
                    // Jika tidak ada cuti dengan status pending
                    return view('addcuti', ['user' => $user, 'pending' => 'N']);
                }
            } else {
                return redirect('/login')->with('error', 'You must be logged in to access the dashboard.');
            }
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Terjadi kesalahan');
        }
    }

    public function PostAddCuti(Request $request)
    {
        try {

            if((int) $request->input('jumlah_cuti') > (int) $request->input('sisa_cuti')){
                return back()->with('error', 'Jumlah cuti melebihi jatah sisa cuti');
            }

            DB::table('history_cuti')->insert([
                'id_karyawan' => session('id'),
                'jumlah_hari' => $request->input('jumlah_cuti'),
                'tanggal_awal' => $request->input('tanggal_mulai'),
                'tanggal_akhir' => $request->input('tanggal_selesai'),
                'alasan_cuti' => $request->input('alasan_cuti'),
                'status' => '1',
            ]);

            //ambil data user
            $user = DB::table('karyawan')
            ->where('id_karyawan', session('id'))
            ->first();

            //kirim email
            $this->SendNotifEmailCuti($user->NAMA, $user->NIK, $request->input('jumlah_cuti'), $request->input('tanggal_mulai'), $request->input('tanggal_selesai'));

            return redirect('/dashboard/riwayat-cuti')->with('success', 'Berhasil ajukan cuti, silahkan tunggu sampai cuti disetujui');
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan input data' . $e->getMessage());
        }
    }

    public function HistoryCuti()
    {
        try{

            // Buat query dasar
            $listCuti = DB::table('history_cuti')
            ->join('karyawan', 'karyawan.id_karyawan', '=', 'history_cuti.id_karyawan')
            ->join('status_cuti', 'status_cuti.id_status_cuti', '=', 'history_cuti.status')
            ->where('karyawan.id_karyawan', session('id'))
            ->select(
                'history_cuti.*', 
                'karyawan.NAMA as nama_karyawan',
                'status_cuti.status as status_cuti'
            )
            ->get();

            return view('riwayatcuti', [
                'listData' => $listCuti
            ]);
            
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Terjadi kesalahan ambil data cuti');
        }
    }

    public function ReportCuti(Request $request)
    {
        try{

            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            if ($request->action == "download") {
                $listCuti = DB::table('history_cuti')
                ->join('karyawan', 'karyawan.id_karyawan', '=', 'history_cuti.id_karyawan')
                ->join('status_cuti', 'status_cuti.id_status_cuti', '=', 'history_cuti.status')
                ->where('addtime', '>=', $startDate . ' 00:00:00')
                ->where('addtime', '<=', $endDate . ' 23:59:59')
                ->select(
                    'history_cuti.*', 
                    'karyawan.NAMA as nama_karyawan',
                    'status_cuti.status as status_cuti'
                )
                ->get();
            
                if ($listCuti->isEmpty()) {
                    return back()->with('error', 'Tidak ada data untuk tanggal yang dipilih.');
                }
            
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
            
                // Header laporan
                $sheet->setCellValue('A1', 'Laporan Cuti');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
                $sheet->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate)));
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
                $sheet->setCellValue('A3', 'Tanggal Generate: ' . date('d-m-Y H:i:s', strtotime('+7 hours')));
                $sheet->mergeCells('A3:G3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
                // Header kolom
                $startRow = 5;
                $sheet->setCellValue("A$startRow", 'No');
                $sheet->setCellValue("B$startRow", 'Nama');
                $sheet->setCellValue("C$startRow", 'Jumlah Cuti');
                $sheet->setCellValue("D$startRow", 'Mulai Cuti');
                $sheet->setCellValue("E$startRow", 'Selesai Cuti');
                $sheet->setCellValue("F$startRow", 'Alasan Cuti');
                $sheet->setCellValue("G$startRow", 'Status Cuti');
            
                // Styling header kolom
                $sheet->getStyle("A$startRow:G$startRow")->getFont()->setBold(true);
                $sheet->getStyle("A$startRow:G$startRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$startRow:G$startRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
                // Mengisi data ke sheet
                $row = $startRow + 1;
                $no = 1;
                foreach ($listCuti as $data) {
                    $sheet->setCellValue("A$row", $no++);
                    $sheet->setCellValue("B$row", $data->nama_karyawan);
                    $sheet->setCellValue("C$row", $data->JUMLAH_HARI . ' Hari');
                    $sheet->setCellValue("D$row", date('d-m-Y', strtotime($data->TANGGAL_AWAL)));
                    $sheet->setCellValue("E$row", date('d-m-Y', strtotime($data->TANGGAL_AKHIR)));
                    $sheet->setCellValue("F$row", $data->alasan_cuti);
                    $sheet->setCellValue("G$row", $data->status_cuti);
            
                    // Menambahkan border pada setiap baris
                    $sheet->getStyle("A$row:G$row")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
                    $row++;
                }
            
                // Auto-size kolom
                foreach (range('A', 'G') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            
                $fileName = 'LaporanCuti_' . date('Ymd', strtotime($startDate)) . '_sampai_' . date('Ymd', strtotime($endDate)) . '.xlsx';
                $filePath = public_path($fileName);
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($filePath);
            
                return response()->download($filePath)->deleteFileAfterSend(true);
            }else{

                $listCuti = DB::table('history_cuti')
                ->join('karyawan', 'karyawan.id_karyawan', '=', 'history_cuti.id_karyawan')
                ->join('status_cuti', 'status_cuti.id_status_cuti', '=', 'history_cuti.status')
                ->where('addtime', '>=', $startDate . ' 00:00:00')
                ->where('addtime', '<=', $endDate . ' 23:59:59')
                ->select(
                    'history_cuti.*', 
                    'karyawan.NAMA as nama_karyawan',
                    'status_cuti.status as status_cuti'
                )
                ->get();

                return view('reportcuti', [
                    'listData' => $listCuti,
                    'startdate' => Carbon::parse($request->start_date)->format('d-m-Y'),
                    'enddate' => Carbon::parse($request->end_date)->format('d-m-Y'),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

            }
            
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Terjadi kesalahan ambil data cuti');
        }
    }

    public function SendEmailCuti($nama, $email, $nik, $status, $jmlCuti, $awalCuti, $akhirCuti)
    {
        try{

            //kirim Email
            $data = [
                'nama_lengkap' => $nama,
                'email' => $email,
                'nik' => $nik,
                'status_cuti' => $status,
                'jumlah_cuti' => $jmlCuti,
                'awal_cuti' => Carbon::parse($awalCuti)->format('d F Y'),
                'akhir_cuti' => Carbon::parse($akhirCuti)->format('d F Y'),
            ];
            
            // Konten HTML untuk email
            $bodyEmail = '
                <html>
                <body>
                    <h1 style="color: #3490dc;">Pengajuan Cuti ' . $data['nama_lengkap'] . '</h1>
                    <p>Berikut adalah informasi Cuti anda:</p>
                    
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
                            <td>Status Cuti</td>
                            <td>' . $data['status_cuti'] . '</td>
                        </tr>
                        <tr>
                            <td>Jumlah Cuti</td>
                            <td>' . $data['jumlah_cuti'] . ' Hari</td>
                        </tr>
                        <tr>
                            <td>Mulai Cuti</td>
                            <td>' . $data['awal_cuti'] . '</td>
                        </tr>
                        <tr>
                            <td>Selesai Cuti</td>
                            <td>' . $data['akhir_cuti'] . '</td>
                        </tr>
                    </table>
            
                    <br>
                    <p>Hubungi Admin jika Anda memiliki kendala terkait Cuti.</p>
                    <p><strong>Admin HRM</strong></p>
                </body>
                </html>
            ';

            Mail::html($bodyEmail, function ($message) use ($data) {
                $message->to($data['email'], $data['nama_lengkap']);
                $message->subject('Informasi Cuti Karyawan');
            });

        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
        }
    }

    public function SendNotifEmailCuti($nama, $nik, $jmlCuti, $awalCuti, $akhirCuti)
    {
        try{

            //kirim Email
            $data = [
                'nama_lengkap' => $nama,
                'nik' => $nik,
                'jumlah_cuti' => $jmlCuti,
                'awal_cuti' => Carbon::parse($awalCuti)->format('d F Y'),
                'akhir_cuti' => Carbon::parse($akhirCuti)->format('d F Y'),
            ];
            
            // Konten HTML untuk email
            $bodyEmail = '
                <html>
                <body>
                    <h1 style="color: #3490dc;">[INFORMASI] Pengajuan Cuti karyawan ' . $data['nama_lengkap'] . '</h1>
                    <p>Berikut adalah informasi Cuti yang diajukan:</p>
                    
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
                            <td>NIK</td>
                            <td>' . $data['nik'] . '</td>
                        </tr>
                        <tr>
                            <td>Jumlah Cuti</td>
                            <td>' . $data['jumlah_cuti'] . ' Hari</td>
                        </tr>
                        <tr>
                            <td>Mulai Cuti</td>
                            <td>' . $data['awal_cuti'] . '</td>
                        </tr>
                        <tr>
                            <td>Selesai Cuti</td>
                            <td>' . $data['akhir_cuti'] . '</td>
                        </tr>
                    </table>
            
                    <br>
                    <p>Segera lakukan Approval cuti pada web dashboard.</p>
                    <p><strong>Admin HRM</strong></p>
                </body>
                </html>
            ';

            //ambil data admin
            $dataEmail = DB::table('karyawan')
            ->where('ROLE', '3')
            ->get();

            foreach ($dataEmail as $admin){
                Mail::html($bodyEmail, function ($message) use ($data, $admin) {
                    $message->to($admin->EMAIL);
                    $message->subject('Informasi Cuti Karyawan');
                });
            }

        }catch(\Exception $e){
            Log::error('Error occurred : ' . $e->getMessage());
        }
    }
    
}
