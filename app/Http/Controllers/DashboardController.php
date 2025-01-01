<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try{

            $user = DB::table('karyawan')
            ->join('bagian', 'karyawan.BAGIAN', '=', 'bagian.ID_BAGIAN') // Melakukan join
            ->join('jabatan', 'karyawan.JABATAN', '=', 'jabatan.ID_JABATAN')
            ->leftJoin('outlet', 'karyawan.OUTLET', '=', 'outlet.ID_OUTLET')
            ->where('email', session('user_id'))
            ->select(
                'karyawan.*', 
                'bagian.BAGIAN as nama_bagian', 
                'jabatan.NAMA_JABATAN as nama_jabatan',
                'outlet.NAMA as nama_toko'
            )
            ->first();

            if ($user) {
                return view('dashboard', ['user' => $user]);
            } else {
                return redirect('/login')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }

        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return redirect('/login')->with('error', 'Terjadi kesalahan ambil data user');
        }
    }
    
}
