<?php

use App\Http\Controllers\API\AnggotaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController as AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BukuController as BukuController;
use App\Http\Controllers\API\PenelitianController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\PesananController;
use App\Http\Controllers\API\SettingController;
use App\Models\Pesanan;
use App\Models\PesananItem;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);
Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');

//Buku
Route::get('buku/{id}/tambahbuku/{count}', [BukuController::class, 'tambahbuku'])->name('tambahbuku')->middleware('auth:sanctum');
Route::post('buku/uploadcover/{id}', [BukuController::class, 'uploadcover'])->name('uploadcover')->middleware('auth:sanctum');
Route::get('buku/CekKetersediaan/{id}', [BukuController::class, 'CekKetersediaan'])->name('CekKetersediaan')->middleware('auth:sanctum');
Route::post('buku/uploadbibliografi/{id}', [BukuController::class, 'uploadbibliografi'])->name('uploadbibliografi')->middleware('auth:sanctum');
Route::get('penelitian/{id}/tambahpenelitian/{count}', [PenelitianController::class, 'tambahpenelitian'])->name('tambahpenelitian')->middleware('auth:sanctum');
Route::post('penelitian/uploadcover/{id}', [PenelitianController::class, 'uploadcover'])->name('penelitianuploadcover')->middleware('auth:sanctum');

Route::get('peminjaman/byKaryaItemId/{id}', [PeminjamanController::class, 'byKaryaItemId'])->name('byKaryaItemId')->middleware('auth:sanctum');
Route::get('setting/last', [SettingController::class, 'getlast'])->name('getlast')->middleware('auth:sanctum');
Route::get('pemesanan/mine', [PesananController::class, 'getmine'])->name('getmine')->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('anggota', AnggotaController::class);
    Route::resource('buku', BukuController::class);
    Route::resource('pemesanan', PesananController::class);
    Route::resource('penelitian', PenelitianController::class);
    Route::resource('pengembalian', PengembalianController::class);
    Route::resource('peminjaman', PeminjamanController::class);
    Route::resource('setting', SettingController::class);
});