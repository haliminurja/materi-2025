<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EnkripsiController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function encrypt(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $method = $request->input('method', 'default');
            $data = $request->input('data', '');
            $key = $request->input('key', '');

            $result = match ($method) {
                'base64' => $this->base64Encrypt($data, $key),
                'sodium' => $this->sodiumEncrypt($data, $key),
                'chacha20' => $this->chacha20Encrypt($data, $key),
                'aes' => $this->aesEncrypt($data, $key),
                default => Crypt::encryptString($data),
            };

             return response()->json([
                'success' => true,
                'message' => 'Berhasil enkripsi',
                'data' => [
                    'method' => $method,
                    'enkripsi' => $result,
                ]
            ], 200);
        }, 'encrypt');
    }

    public function decrypt(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $method = $request->input('method', 'default');
            $data = $request->input('data', '');
            $key = $request->input('key', '');

            $result = match ($method) {
                'base64' => $this->base64Decrypt($data, $key),
                'sodium' => $this->sodiumDecrypt($data, $key),
                'chacha20' => $this->chacha20Decrypt($data, $key),
                'aes' => $this->aesDecrypt($data, $key),
                default => Crypt::decryptString($data),
            };

             return response()->json([
                'success' => true,
                'message' => 'Berhasil dekripsi',
                'data' => [
                    'method' => $method,
                    'dekripsi' => $result,
                ]
            ], 200);

        }, 'dekripsi');
    }

    private function chacha20Encrypt(string $data, string $key)
    {
        try {
            $secretKey = $this->generateKey($key);
            $nonce = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES);
            $ciphertext = sodium_crypto_aead_chacha20poly1305_encrypt($data, '', $nonce, $secretKey);
            return base64_encode($nonce . $ciphertext);
        } catch (Exception) {
            return null;
        }
    }

    private function chacha20Decrypt(string $data, string $key)
    {
        try {
            $decoded = base64_decode($data, true);
            if ($decoded === false || strlen($decoded) < SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES) {
                return null;
            }

            $secretKey = $this->generateKey($key);
            $nonce = substr($decoded, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES);
            $ciphertext = substr($decoded, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES);

            return sodium_crypto_aead_chacha20poly1305_decrypt($ciphertext, '', $nonce, $secretKey) ?: null;
        } catch (Exception) {
            return null;
        }
    }

    private function aesEncrypt(string $data, string $key)
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-gcm', $this->generateKey($key), OPENSSL_RAW_DATA, $iv, $tag);
        return base64_encode($iv . $tag . $encrypted);
    }

    private function aesDecrypt(string $data, string $key)
    {
        $decoded = base64_decode($data, true);
        if ($decoded === false || strlen($decoded) < 32) {
            return null;
        }

        $iv = substr($decoded, 0, 16);
        $tag = substr($decoded, 16, 16);
        $ciphertext = substr($decoded, 32);
        return openssl_decrypt($ciphertext, 'aes-256-gcm', $this->generateKey($key), OPENSSL_RAW_DATA, $iv, $tag) ?: null;
    }

    private function base64Encrypt(string $data, string $key)
    {
        $hashedKey = $this->generateKey($key);
        return base64_encode($hashedKey . $data);
    }

    private function base64Decrypt(string $data, string $key)
    {
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return null;
        }

        $hashedKey = $this->generateKey($key);
        if (!str_starts_with($decoded, $hashedKey)) {
            return null;
        }

        return substr($decoded, strlen($hashedKey));
    }

    private function sodiumEncrypt(string $data, string $key)
    {
        try {
            $secretKey = $this->generateSodiumKey($key);
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            return base64_encode($nonce . sodium_crypto_secretbox($data, $nonce, $secretKey));
        } catch (Exception) {
            return null;
        }
    }

    private function sodiumDecrypt(string $data, string $key)
    {
        try {
            $decoded = base64_decode($data, true);
            if ($decoded === false || strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
                return null;
            }

            $secretKey = $this->generateSodiumKey($key);
            $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

            return sodium_crypto_secretbox_open($ciphertext, $nonce, $secretKey) ?: null;
        } catch (Exception) {
            return null;
        }
    }

    private function generateKey(string $key)
    {
        return hash('sha256', $key, true);
    }

    private function generateSodiumKey(string $key)
    {
        return sodium_crypto_generichash($this->generateKey($key), '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }
}
