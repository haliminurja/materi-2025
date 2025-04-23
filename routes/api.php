<?php

use App\Http\Controllers\EnkripsiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\RedisController;
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

Route::prefix('redis')->group(function () {
    Route::post('set', [RedisController::class, 'set']);
    Route::get('get/{key}', [RedisController::class, 'get']);
    Route::delete('delete/{key}', [RedisController::class, 'delete']);
    Route::get('exists/{key}', [RedisController::class, 'exists']);
    Route::post('flush', [RedisController::class, 'flushAll']);
    Route::get('handle-cache', [RedisController::class, 'handleWithCache']);
    Route::post('delete-by-pattern', [RedisController::class, 'deleteByPattern']);
});

Route::post('encrypt', [EnkripsiController::class, 'encrypt']);
Route::post('decrypt', [EnkripsiController::class, 'decrypt']);
