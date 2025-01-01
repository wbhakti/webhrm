<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PresensiController extends Controller
{

    public function Absensi()
    {
        try {
            $listAbsen = DB::table('presensi')
                ->where('id_karyawan', session('id'))
                ->whereDate('addtime', Carbon::now()->addHours(7))
                ->orderBy('addtime', 'asc')
                ->get();

            $lastRow = $listAbsen->last();

            // Default tombol enable
            $disableDatangButton = false;
            $disablePulangButton = false;

            return view('absensi', [
                'listData' => $listAbsen,
                'disableDatangButton' => $disableDatangButton,
                'disablePulangButton' => $disablePulangButton
            ]);
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('absensi', ['error' => 'Terjadi kesalahan load data']);
        }
    }

    public function uploadfoto(Request $request)
    {
        try{

            // Validasi file
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Ambil file gambar
            $image = $request->file('photo');
            $imagePath = public_path('uploads');
            $fileName = $image->getClientOriginalName();

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
            }else{
                return response()->json([
                    'success' => false,
                ]);
            }

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
                'status_absen' => $request->input('status'),
                'nama_karyawan' => session('nama'),
                'foto_kehadiran' => $request->file('photo')->getClientOriginalName(),
                'id_karyawan' => session('id'),
                'outlet' => session('outlet'),
                'addtime' => Carbon::now()->addHours(7)->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'success' => true,
            ]);

        }catch (\Exception $e) {
            Log::error('uploadfoto Error occurred report : ' . $e->getMessage());
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function ReportAbsensi()
    {
        try{

            //cek user
            $user = DB::table('karyawan')
            ->where('id_karyawan', session('id'))
            ->first();

            if($user->JABATAN == '10'){
                return view('reportabsensi');
            }else{
                if($user->ROLE == '3'){
                    //khusus HRD ada filter outlet
                    $listOutlet = DB::table('outlet')->get();
                    return view('reportabsensi', [
                        'outlets' => $listOutlet
                    ]);
                }
                return back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
        }catch(\Exception $e){
            Log::error('ReportAbsensi Error occurred report : ' . $e->getMessage());
            return view('reportabsensi', ['error' => 'Terjadi kesalahan load data']);
        }
    }

    public function PostReportAbsen(Request $request)
    {
        try {

            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $outlet = $request->outlet ?? session('outlet');
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if($request->action == "report")
            {
                $listAbsen = DB::table('presensi')
                ->where('outlet', $outlet)
                ->where('addtime', '>=', $startDate . ' 00:00:00')
                ->where('addtime', '<=', $endDate . ' 23:59:59')
                ->orderBy('nama_karyawan')
                ->orderBy('addtime')
                ->get();

                $processedData = [];
                foreach ($listAbsen as $absen) {
                    $tanggal = date('Y-m-d', strtotime($absen->addtime));
                    $key = $absen->nama_karyawan . '_' . $tanggal;

                    // Pastikan ada array untuk setiap karyawan pada tanggal tersebut
                    if (!isset($processedData[$key])) {
                        $processedData[$key] = [
                            'name' => $absen->nama_karyawan,
                            'absen' => [] // Menyimpan semua datang dan pulang
                        ];
                    }

                    // Menambahkan datang dan pulang dalam array absensi
                    if ($absen->status_absen === 'DATANG') {
                        $processedData[$key]['absen'][] = [
                            'datang' => $absen->addtime,
                            'foto_datang' => $absen->foto_kehadiran,
                            'pulang' => null,
                            'foto_pulang' => null
                        ];
                    } elseif ($absen->status_absen === 'PULANG') {
                        // Jika sudah ada datang, update dengan pulang
                        $lastIndex = count($processedData[$key]['absen']) - 1;
                        if ($lastIndex >= 0 && $processedData[$key]['absen'][$lastIndex]['pulang'] === null) {
                            $processedData[$key]['absen'][$lastIndex]['pulang'] = $absen->addtime;
                            $processedData[$key]['absen'][$lastIndex]['foto_pulang'] = $absen->foto_kehadiran;
                        }
                    }
                }

                $attendances = array_values($processedData);

                // Cek user
                $listOutlet = collect(); 
                if (session('role') == '3') {
                    $listOutlet = DB::table('outlet')->get();
                }

                return view('reportabsensi', [
                    'attendances' => $attendances,
                    'startdate' => Carbon::parse($startDate)->format('d-m-Y'),
                    'enddate' => Carbon::parse($endDate)->format('d-m-Y'),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'outlets' => $listOutlet
                ]);
            }
            else
            {
                //download
                $listAbsen = DB::table('presensi')
                ->where('outlet', $outlet)
                ->where('addtime', '>=', $startDate . ' 00:00:00')
                ->where('addtime', '<=', $endDate . ' 23:59:59')
                ->orderBy('nama_karyawan')
                ->orderBy('addtime')
                ->get();

                if ($listAbsen->isEmpty()) {
                    return back()->with('error', 'Tidak ada data untuk tanggal yang dipilih.');
                }

                $processedData = [];
                foreach ($listAbsen as $absen) {
                    $tanggal = date('Y-m-d', strtotime($absen->addtime));
                    $key = $absen->nama_karyawan . '_' . $tanggal;

                    // Pastikan ada array untuk setiap karyawan pada tanggal tersebut
                    if (!isset($processedData[$key])) {
                        $processedData[$key] = [
                            'name' => $absen->nama_karyawan,
                            'absen' => [] // Menyimpan semua datang dan pulang
                        ];
                    }

                    // Menambahkan datang dan pulang dalam array absensi
                    if ($absen->status_absen === 'DATANG') {
                        $processedData[$key]['absen'][] = [
                            'datang' => $absen->addtime,
                            'foto_datang' => $absen->foto_kehadiran,
                            'pulang' => null,
                            'foto_pulang' => null
                        ];
                    } elseif ($absen->status_absen === 'PULANG') {
                        // Jika sudah ada datang, update dengan pulang
                        $lastIndex = count($processedData[$key]['absen']) - 1;
                        if ($lastIndex >= 0 && $processedData[$key]['absen'][$lastIndex]['pulang'] === null) {
                            $processedData[$key]['absen'][$lastIndex]['pulang'] = $absen->addtime;
                            $processedData[$key]['absen'][$lastIndex]['foto_pulang'] = $absen->foto_kehadiran;
                        }
                    }
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                //header kolom
                $sheet->setCellValue('A1', 'Laporan Absensi');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                //ambil outlet
                $namaOutlet = DB::table('outlet')
                ->where('id_outlet', $outlet)
                ->first();

                $sheet->setCellValue('A2', $namaOutlet->NAMA);
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A3', 'Periode: ' . date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate)));
                $sheet->mergeCells('A3:G3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal Generate: ' . date('d-m-Y H:i:s', strtotime('+7 hours')));
                $sheet->mergeCells('A4:G4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $startRow = 6;

                // Header kolom
                $sheet->setCellValue("A$startRow", 'No');
                $sheet->setCellValue("B$startRow", 'Nama');
                $sheet->setCellValue("C$startRow", 'Tanggal');
                $sheet->setCellValue("D$startRow", 'Jam Datang');
                $sheet->setCellValue("E$startRow", 'Jam Pulang');
                $sheet->setCellValue("F$startRow", 'Total Jam Kerja');
                $sheet->setCellValue("G$startRow", 'Keterangan');

                // Styling header kolom
                $sheet->getStyle("A$startRow:G$startRow")->getFont()->setBold(true);
                $sheet->getStyle("A$startRow:G$startRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$startRow:G$startRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                //data ke sheet
                $row = $startRow + 1;
                $no = 1;
                foreach ($processedData as $data) {
                    foreach ($data['absen'] as $absen){
                        $sheet->setCellValue("A$row", $no++);
                        $sheet->setCellValue("B$row", $data['name']);
                        $sheet->setCellValue("C$row", $absen['datang'] ? date('d-m-Y', strtotime($absen['datang'])) : '-');
                        $sheet->setCellValue("D$row", $absen['datang'] ? date('H:i:s', strtotime($absen['datang'])) : '-');
                        $sheet->setCellValue("E$row", $absen['pulang'] ? date('H:i:s', strtotime($absen['pulang'])) : '-');

                        if ($absen['datang'] && $absen['pulang']) {
                            $datangTimestamp = strtotime($absen['datang']);
                            $pulangTimestamp = strtotime($absen['pulang']);
                            $totalSeconds = $pulangTimestamp - $datangTimestamp;

                            // Format "X Jam Y Menit Z Detik"
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            $seconds = $totalSeconds % 60;
                            $totalTime = sprintf('%d Jam %d Menit %d Detik', $hours, $minutes, $seconds);
                        } else {
                            $totalTime = '-';
                        }

                        $sheet->setCellValue("F$row", $totalTime);

                        if (!$absen['datang']) {
                            $keterangan = 'Lupa absen datang';
                        } elseif (!$absen['pulang']) {
                            $keterangan = 'Lupa absen pulang';
                        } else {
                            $keterangan = '-';
                        }

                        $sheet->setCellValue("G$row", $keterangan);
                        $row++;
                    }
                }

                // Auto-size kolom
                foreach (range('A', 'G') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                $fileName = 'LaporanAbsen_' . $startDate . '_sampai_' . $endDate . '.xlsx';
                $filePath = public_path($fileName);
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($filePath);

                return response()->download($filePath)->deleteFileAfterSend(true);
            }

        } catch (\Exception $e) {
            Log::error('PostReportAbsen Error occurred report: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan ambil data.');
        }
    }
       
}