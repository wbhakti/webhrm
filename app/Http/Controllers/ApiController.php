<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class ApiController  extends Controller
{
    
    public function ValidationUser(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'username' => 'required|string|max:50'
            ]);

            $dataUser = DB::table('karyawan')
            ->select('id_karyawan', 'nama', 'outlet')
            ->where('email', $validatedData['username'])
            ->first();

            if ($dataUser) {
                return response()->json([
                    'endpoint' => 'validateuser',
                    'responseCode' => '0',
                    'responseMessage' => 'user valid',
                    'data' => $dataUser
                ], 200);
            }else{
                return response()->json([
                    'endpoint' => 'validateuser',
                    'responseCode' => '1',
                    'responseMessage' => 'presensi gagal [user tidak valid]',
                    'data' => null
                ], 200);
            }

        } catch (\Exception $e) {
            
            Log::error('ValidationUser Error occurred : ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'validateuser',
                'responseCode' => '1',
                'responseMessage' => 'presensi gagal [user exception error]',
                'data' => null
            ], 200);

        }
    }

    public function SendAbsensi(Request $request)
    {
        try{

            $validatedData = $request->validate([
                'nama_karyawan' => 'required|string',
                'id_karyawan' => 'required|string',
                'status_absen' => 'required|string',
                'outlet' => 'required|string',
                'foto_absen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'alamat' => 'required|string',
            ]);

            if ($request->hasFile('foto_absen')) {
                
                $image = $request->file('foto_absen');
                $yearMonth = Carbon::now()->format('Y-m');
                $imagePath = public_path("uploads/$yearMonth");
                
                // Buat folder jika belum ada
                if (!File::exists($imagePath)) {
                    File::makeDirectory($imagePath, 0755, true, true);
                }
                
                $fileName = 'selfie' . $validatedData['status_absen'] . '_' . Carbon::now()->valueOf() . '.png'; //$image->getClientOriginalName();

                // kompres image
                $mimeType = $image->getMimeType();
                list($width, $height) = getimagesize($image->getRealPath());
                $newWidth = 300;
                $newHeight = 200;

                $tmp = imagecreatetruecolor($newWidth, $newHeight);

                if ($mimeType === 'image/jpeg') {
                    $source = imagecreatefromjpeg($image->getRealPath());
                } elseif ($mimeType === 'image/png') {
                    $source = imagecreatefrompng($image->getRealPath());
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                } else {
                    return response()->json([
                        'endpoint' => 'absenuser',
                        'responseCode' => '1',
                        'responseMessage' => 'presensi gagal [image file tidak valid]'
                    ], 200);
                }

                // Pilih font dan ukuran
                $font = public_path('arial.ttf');
                $fontSize = 12;
                $textColor = imagecolorallocate($source, 255, 255, 255); // Warna putih
                $x = 10; // Posisi horizontal margin kiri
                $y = imagesy($source) - 50; // Posisi vertical margin bawah
                $lineHeight = 20; // Jarak antar baris

                // Pecah teks alamat
                $maxWidth = imagesx($source) - 20; //Lebar maksimum teks
                $words = explode(' ', $validatedData['alamat']);
                $lines = [];
                $currentLine = '';

                foreach ($words as $word) {
                    $testLine = $currentLine . ($currentLine === '' ? '' : ' ') . $word;
                    $bbox = imagettfbbox($fontSize, 0, $font, $testLine);
                    $lineWidth = $bbox[2] - $bbox[0];

                    if ($lineWidth > $maxWidth) {
                        $lines[] = $currentLine;
                        $currentLine = $word;
                    } else {
                        $currentLine = $testLine;
                    }
                }

                if (!empty($currentLine)) {
                    $lines[] = $currentLine;
                }

                //teks ke gambar baris per baris
                foreach ($lines as $line) {
                    imagettftext($source, $fontSize, 0, $x, $y, $textColor, $font, $line);
                    $y += $lineHeight;
                }

                // Tambah timestamp
                $timestamp = Carbon::now()->addHours(7)->format('Y-m-d H:i:s');
                $xTimestamp = 10; // Posisi horizontal margin kiri
                $yTimestamp = 20; // Posisi atas kiri
                imagettftext($source, $fontSize, 0, $xTimestamp, $yTimestamp, $textColor, $font, $timestamp);

                // Resize gambar
                imagecopyresampled($tmp, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                if ($mimeType === 'image/jpeg') {
                    imagejpeg($tmp, $imagePath . '/' . $fileName, 80); // JPEG kualitas 80%
                } elseif ($mimeType === 'image/png') {
                    imagepng($tmp, $imagePath . '/' . $fileName, 8); // PNG kompresi level 8
                }

                imagedestroy($tmp);
                imagedestroy($source);

                //insert
                DB::table('presensi')->insert([
                    'status_absen' => strtoupper($validatedData['status_absen']),
                    'nama_karyawan' => strtoupper($validatedData['nama_karyawan']),
                    'foto_kehadiran' => "/$yearMonth/$fileName",
                    'id_karyawan' => $validatedData['id_karyawan'],
                    'outlet' => $validatedData['outlet'],
                    'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'endpoint' => 'absenuser',
                    'responseCode' => '0',
                    'responseMessage' => 'presensi karyawan ' . strtoupper($validatedData['nama_karyawan']) . ' sukses'
                ], 200);

            }else{

                return response()->json([
                    'endpoint' => 'absenuser',
                    'responseCode' => '1',
                    'responseMessage' => 'presensi gagal [image file tidak valid]'
                ], 200);

            }

        }catch (\Exception $e) {
            Log::error('Gagal absenuser: ' . $e->getMessage());

            return response()->json([
                'endpoint' => 'absenuser',
                'responseCode' => '1',
                'responseMessage' => 'presensi gagal [exception error]'
            ], 200);
        }
    }

}
