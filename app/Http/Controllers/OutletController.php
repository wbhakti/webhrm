<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutletController extends Controller
{
    public function ListOutlet(Request $request)
    {
        try{

            $query = DB::table('outlet')
            ->join('tipe_outlet', 'outlet.tipe', '=', 'tipe_outlet.ID_TIPE')
            ->join('status_outlet', 'outlet.status', '=', 'status_outlet.ID_STATUS_OUTLET')
            ->select(
                'outlet.*', 
                'tipe_outlet.JENIS as jenis_outlet',
                'status_outlet.STATUS as status_outlet',
                'tipe_outlet.ID_TIPE as id_tipe_outlet',
                'status_outlet.ID_STATUS_OUTLET as id_status_outlet'
            );

            // Jika ada filter status, tambahkan kondisi where
            $filter = $request->input('status');
            if (!empty($filter)) {

                if($filter == "pribadi"){
                    $query->where('status_outlet.ID_STATUS_OUTLET', '1');
                }
                else if($filter == "franchise"){
                    $query->where('status_outlet.ID_STATUS_OUTLET', '2');
                }
                else if($filter == "reguler"){
                    $query->where('tipe_outlet.ID_TIPE', '1');
                }
                else if($filter == "express"){
                    $query->where('tipe_outlet.ID_TIPE', '2');
                }
            }

            //data final
            $listOutlet = $query->get();
            
            return view('listoutlet', [
                'listData' => $listOutlet
            ]);
        }catch(\Exception $e){
            Log::error('Error occurred report : ' . $e->getMessage());
            return view('listoutlet', ['error' => 'Terjadi kesalahan load data']);
        }
    }

    public function AddOutlet()
    {
        return view('addoutlet');
    }

    public function PostOutlet(Request $request)
    {
        try {

            $tmpProvinsi = explode('|', $request->input('provinsi'));
            $provinsi = $tmpProvinsi[1];
            $tmpKota = explode('|', $request->input('kabupaten'));
            $kabupaten = $tmpKota[1];
            $tmpKecamatan = explode('|', $request->input('kecamatan'));
            $kecamatan = $tmpKecamatan[1];
            $tmpKelurahan = explode('|', $request->input('kelurahan'));
            $kelurahan = $tmpKelurahan[1];

            // Simpan data registrasi
            DB::table('outlet')->insert([
                'nama' => $request->input('nama'),
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
                'alamat' => $request->input('alamat'),
                'no_hp' => $request->input('no_hp'),
                'tipe' => $request->input('tipe'),
                'status' => $request->input('status'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            return redirect('/dashboard/list-outlet')->with('success', 'Berhasil simpan data Outlet');
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan input data');
        }
    }

    public function EditOutlet(Request $request)
    {
        try {

            $tmpProvinsi = explode('|', $request->input('provinsi'));
            $provinsi = $tmpProvinsi[1];
            $tmpKota = explode('|', $request->input('kabupaten'));
            $kabupaten = $tmpKota[1];
            $tmpKecamatan = explode('|', $request->input('kecamatan'));
            $kecamatan = $tmpKecamatan[1];
            $tmpKelurahan = explode('|', $request->input('kelurahan'));
            $kelurahan = $tmpKelurahan[1];

            DB::table('outlet')
            ->where('id_outlet', $request->input('rowid'))
            ->update([
                'nama' => $request->input('nama'),
                'kelurahan' => $kelurahan,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
                'alamat' => $request->input('alamat'),
                'no_hp' => $request->input('no_hp'),
                'tipe' => $request->input('tipe'),
                'status' => $request->input('status'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            return redirect('/dashboard/list-outlet')->with('success', 'Berhasil Edit data Outlet');
        } catch (\Exception $e) {
            Log::error('Error occurred report : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan edit data');
        }
    }

    public function getProvinsi()
    {
        try{

            $listProvinsi = DB::table('provinces')->get();
            return response()->json($listProvinsi);
            
        }catch(\Exception $e){
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }
    public function getKota($id)
    {
        try{

            $tmpData = explode('|', $id);
            $dataId = $tmpData[0];

            $listKota = DB::table('cities')
            ->where('prov_id', $dataId)
            ->get();

            return response()->json($listKota);
            
        }catch(\Exception $e){
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }
    public function getKecamatan($id)
    {
        try{

            $tmpData = explode('|', $id);
            $dataId = $tmpData[0];
    
            $listKecamatan = DB::table('districts')
            ->where('city_id', $dataId)
            ->get();
    
            return response()->json($listKecamatan);
            
        }catch(\Exception $e){
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }
    public function getKelurahan($id)
    {
        try{

            $tmpData = explode('|', $id);
            $dataId = $tmpData[0];
            
            $listKelurahan = DB::table('subdistricts')
            ->where('dis_id', $dataId)
            ->get();
    
            return response()->json($listKelurahan);
            
        }catch(\Exception $e){
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }
    public function getOutlet()
    {
        try{

            $listOutlet = DB::table('outlet')->get();
    
            return response()->json($listOutlet);
            
        }catch(\Exception $e){
            return response()->json(['message' => 'Terjadi kesalahan.'], 400);
        }
    }
    
}
