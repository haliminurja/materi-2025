<?php

use App\Http\Controllers\EnkripsiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MailerController;
use Illuminate\Support\Facades\Route;


Route::post('login', [MahasiswaController::class, 'login']);
Route::post('register', [MahasiswaController::class, 'store']);

Route::post('send',[MailerController::class,'send']);
Route::post('verifikasi',[MailerController::class,'verifikasi']);

Route::prefix('mahasiswa')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MahasiswaController::class, 'index']);
    Route::post('/', [MahasiswaController::class, 'store']);
    Route::get('/{nim}', [MahasiswaController::class, 'show']);
    Route::put('/{nim}', [MahasiswaController::class, 'update']);
    Route::delete('/{nim}', [MahasiswaController::class, 'destroy']);
});

Route::post('encrypt', [EnkripsiController::class, 'encrypt']);
Route::post('decrypt', [EnkripsiController::class, 'decrypt']);
