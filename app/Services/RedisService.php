<?php

namespace App\Services;

use Exception;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class RedisService
{
    private const int defaultTtl = 600; // TTL (time-to-live) default: 600 detik
    private const int maxRetries = 3;   // Maksimal percobaan ulang jika Redis gagal
    private const int retryDelay = 500; // Delay awal (ms) antar retry
    private Connection $connection;     // Objek koneksi ke Redis

    public function __construct(RedisManager $redisManager)
    {
        $this->connection = $redisManager->connection();
    }

    //Menghapus data redis berdasar key
    public function delete(string $key): bool
    {
        return (bool) $this->executeCommand(fn () => $this->connection->del($key));
    }

    //Menghapus Banyak Key Berdasarkan Pola
    public function deleteKeysByPattern(string $pattern): int
    {
        return (int) $this->executeCommand(function () use ($pattern): int {
            $keys = $this->connection->keys($pattern);
            if (! empty($keys)) {
                $this->connection->transaction(static function ($tx) use ($keys): void {
                    $tx->del($keys);
                });

                return count($keys);
            }

            return 0;
        });
    }

    private function executeCommand(callable $operation)
    {
        $attempts = 0;
        $delay = self::retryDelay;
        while ($attempts < self::maxRetries) {
            try {
                return $operation();
            } catch (Exception $e) {
                Log::error(sprintf('Redis command failed: %s (Attempt: %d)', $e->getMessage(), $attempts));
                if ($attempts === self::maxRetries - 1) {
                    break;
                }

                Sleep::usleep($delay * 1000);
                $delay *= 2;
                $attempts++;
            }
        }

        Log::warning(sprintf('Redis command failed after %d attempts.', self::maxRetries));

        return null;

    }

    //Mengecek Eksistensi Key
    public function exists(string $key): bool
    {
        return (bool) $this->executeCommand(fn () => $this->connection->exists($key));
    }

    //Menghapus Semua Key
    public function flushAll(): void
    {
        $this->executeCommand(fn () => $this->connection->flushAll());
    }

    //Mendapatkan data berdasarkan key
    public function get(string $key): ?string
    {
        return $this->executeCommand(fn () => $this->connection->get($key));
    }

    //Menyimpan data berdasarkan key
    public function set(string $key, string $value, ?int $ttl = null): void
    {
        $this->executeCommand(function () use ($key, $value, $ttl): void {
            $ttl ??= self::defaultTtl;
            $this->connection->pipeline(static function ($pipe) use ($key, $value, $ttl): void {
                $pipe->set($key, $value);
                $pipe->expire($key, $ttl);
            });
        });
    }

    //membuat pola cek data, simpan, lalu tampilkan.
    public function handleWithCache(string $cacheKey, callable $callback, ?int $cacheDuration = null)
    {
        $result = $this->get($cacheKey);
        if ($result !== null) {
            return json_decode($result);
        }

        $result = $callback();

        if ($result === null || ($result === [])) {
            return null;
        }

        if (is_array($result) || is_object($result)) {
            $result = json_encode($result);
            $this->set($cacheKey, $result, $cacheDuration);

            return json_decode($result);
        }

        return $result;
    }

}
