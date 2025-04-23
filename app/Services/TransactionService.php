<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionService
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function handleWithTransaction(callable $callback, string $list  = '-')
    {
        try {
            return DB::transaction(function () use ($list, $callback) {
                // Eksekusi callback utama terlebih dahulu
                $result = $callback();

                // Log activity setelah operasi utama berhasil
                $this->logService->logActivity($list);

                return $result;
            });
        } catch (QueryException $qe) {
            //simpan log eror jika kesalahan pada stuktur database, seperti relasi, tipe data, dan lain-lainya
            $message = $this->getQueryError($qe->getCode());

            $this->logService->logError($list, $qe);

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => $qe->getMessage(),
            ], 500);
        } catch (Throwable $th) {
            //simpan log eror jika kesalahan pada aplikasi
            $this->logService->logError($list, $th);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function handleWithLogDB($list, $table, $id_table, $data)
    {
        $this->logService->logDatabase($list, $table, $id_table, $data);
    }

    private function getQueryError(string $errorCode)
    {
        $defaultMessage = 'Terjadi kesalahan pada basis data. Silakan coba lagi nanti.';
        $errorMessages = [
            '23000' => 'Operasi tidak dapat diselesaikan karena pelanggaran integritas data.',
            '23505' => 'Terjadi pelanggaran batasan unik. Pastikan semua data bersifat unik.',
            '23503' => 'Terjadi pelanggaran batasan kunci asing. Periksa dependensi data.',
            '42P01' => 'Tabel yang ditentukan tidak ada. Periksa nama tabel dan coba lagi.',
            '42703' => 'Kolom yang ditentukan tidak ditemukan dalam basis data.',
            '42601' => 'Terdapat kesalahan sintaks pada kueri SQL. Periksa sintaks kueri.',
            '40001' => 'Terjadi kesalahan tingkat isolasi transaksi. Silakan coba ulang transaksi.',
            '40P01' => 'Deadlock terdeteksi. Silakan coba ulang operasi.',
            '22007' => 'Format tanggal/waktu tidak valid. Perbaiki format dan coba lagi.',
            '22008' => 'Overflow pada field tanggal/waktu. Sesuaikan nilai tanggal/waktu.',
            '23502' => 'Pelanggaran batasan not-null. Pastikan semua field yang diperlukan diisi.',
            '23514' => 'Terjadi pelanggaran batasan cek. Pastikan data memenuhi semua batasan.',
        ];

        return $errorMessages[$errorCode] ?? $defaultMessage;
    }
}
