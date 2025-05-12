<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//role super admin & admin HRD
Route::group(['middleware' => ['checkLogin', 'checkRole:1,3']], function () {
    Route::get('/dashboard/list-karyawan', 'App\Http\Controllers\UserController@ListEmploye');
    Route::get('/dashboard/tambah-karyawan', 'App\Http\Controllers\UserController@AddEmploye');
    Route::post('/postaddkaryawan', 'App\Http\Controllers\UserController@PostEmploye');
    Route::post('/posteditkaryawan', 'App\Http\Controllers\UserController@EditEmploye');
    Route::post('/postdeletekaryawan', 'App\Http\Controllers\UserController@DeleteEmploye');
    Route::post('/resetpasswordkaryawan', 'App\Http\Controllers\UserController@ResetPasswordEmploye');
    Route::get('/dashboard/list-outlet', 'App\Http\Controllers\OutletController@ListOutlet');
    Route::get('/dashboard/tambah-outlet', 'App\Http\Controllers\OutletController@AddOutlet');
    Route::post('/postaddoutlet', 'App\Http\Controllers\OutletController@PostOutlet');
    Route::post('/posteditoutlet', 'App\Http\Controllers\OutletController@EditOutlet');
    Route::get('/dashboard/approval-cuti', 'App\Http\Controllers\CutiController@ApprovalCuti');
    Route::post('/postapprovalcuti', 'App\Http\Controllers\CutiController@PostApprovalCuti');
    Route::get('/get-jabatan/{id}', 'App\Http\Controllers\UserController@getJabatan');
    Route::get('/get-outlet', 'App\Http\Controllers\OutletController@getOutlet');
    Route::get('/dashboard/report-cuti', 'App\Http\Controllers\CutiController@ReportCuti');
    Route::get('/get-provinsi', 'App\Http\Controllers\OutletController@getProvinsi');
    Route::get('/get-kota/{id}', 'App\Http\Controllers\OutletController@getKota');
    Route::get('/get-kecamatan/{id}', 'App\Http\Controllers\OutletController@getKecamatan');
    Route::get('/get-kelurahan/{id}', 'App\Http\Controllers\OutletController@getKelurahan');
});


//role user & admin data
Route::group(['middleware' => ['checkLogin', 'checkRole:2,4']], function () {
    Route::get('/dashboard/riwayat-cuti', 'App\Http\Controllers\CutiController@HistoryCuti');
    Route::get('/dashboard/pengajuan-cuti', 'App\Http\Controllers\CutiController@AddCuti');
    Route::post('/postaddcuti', 'App\Http\Controllers\CutiController@PostAddCuti');
    Route::get('/dashboard/absensi', 'App\Http\Controllers\PresensiController@Absensi');
    Route::post('/upload-foto', 'App\Http\Controllers\PresensiController@uploadfoto');
});

//bebas role admin & user
Route::group(['middleware' => 'checkLogin'], function () {
    Route::get('/', 'App\Http\Controllers\DashboardController@index');
    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
    Route::get('/dashboard/ubah-password', 'App\Http\Controllers\UserController@ChangePassword');
    Route::post('/postubahpassword', 'App\Http\Controllers\UserController@PostChangePassword');
    Route::get('/dashboard/report-absensi', 'App\Http\Controllers\PresensiController@ReportAbsensi');
    Route::get('/getreportabsen', 'App\Http\Controllers\PresensiController@PostReportAbsen');
});

//bebas auth, halaman awal
Route::get('/lupa-password', 'App\Http\Controllers\UserController@ResetPassword')->name('resetpassword');
Route::get('/logout', 'App\Http\Controllers\UserController@logout')->name('logout');
Route::get('/login', 'App\Http\Controllers\UserController@login')->name('login');
Route::post('/postlogin', 'App\Http\Controllers\UserController@postlogin');
Route::post('/postresetpassword', 'App\Http\Controllers\UserController@PostResetPassword');