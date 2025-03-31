<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

Route::prefix('mahasiswa')->group(function () {
    //curl -X GET "http://localhost:8000/api/mahasiswa" -H "Accept: application/json"
    Route::get('/', [MahasiswaController::class, 'index']);
    // curl -X POST "http://localhost:8000/api/mahasiswa" \
    //  -H "Content-Type: application/json" \
    //  -H "Accept: application/json" \
    //  -d '{
    //        "nim": "220101001",
    //        "nama": "Budi Santoso",
    //        "jenis_kelamin": "L",
    //        "password": "password123"
    //      }'
    Route::post('/', [MahasiswaController::class, 'store']);
    //curl -X GET "http://localhost:8000/api/mahasiswa/220101001" -H "Accept: application/json"
    Route::get('/{nim}', [MahasiswaController::class, 'show']);
    // curl -X PUT "http://localhost:8000/api/mahasiswa/220101001" \
    //  -H "Content-Type: application/json" \
    //  -H "Accept: application/json" \
    //  -d '{
    //        "nama": "Budi Santoso Update",
    //        "jenis_kelamin": "L",
    //        "password": "newpassword123"
    //      }'
    Route::put('/{nim}', [MahasiswaController::class, 'update']);
    //curl -X DELETE "http://localhost:8000/api/mahasiswa/220101001" -H "Accept: application/json"
    Route::delete('/{nim}', [MahasiswaController::class, 'destroy']);
});
