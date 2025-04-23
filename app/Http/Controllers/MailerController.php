<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\Mahasiswa;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailerController extends Controller
{

    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $mahasiswa = Mahasiswa::where('email', $request->email)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan',
                ], 404);
            }

            $otp = $this->generateSecureOTP();

            Mail::to($request->email)->send(new OTPMail($otp));

            $data = [
                'otp' => $otp
            ];

            $mahasiswa->update($data);

            $this->transactionService->handleWithLogDB('Kirim OTP', 'mahasiswa', $mahasiswa->nim, $data);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP sudah dikirim',
            ]);
        }, 'Kirim OTP');
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $mahasiswa = Mahasiswa::where('otp', $request->otp)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP tidak cocok atau sudah tidak berlaku',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi OTP berhasil',
                'data' => $mahasiswa
            ]);
        }, 'Verifikasi OTP');
    }


    private function generateSecureOTP(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        return implode('', array_map(
            fn() => $characters[random_int(0, $charactersLength - 1)],
            range(1, $length)
        ));
    }
}
