<?php

namespace App\Http\Controllers;

use App\Services\RedisService;
use Illuminate\Http\Request;

class RedisController extends Controller
{
    protected RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    public function set(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value');
        $ttl = $request->input('ttl', 120);

        $this->redisService->set($key, $value, $ttl);

        return response()->json(['message' => "Key '$key' set successfully."]);
    }

    public function get($key)
    {
        $value = $this->redisService->get($key);

        if ($value === null) {
            return response()->json(['message' => "Key '$key' not found."], 404);
        }

        return response()->json(['key' => $key, 'value' => $value]);
    }

    public function delete($key)
    {
        $deleted = $this->redisService->delete($key);

        return response()->json(['message' => $deleted ? "Key '$key' deleted." : "Key '$key' not found."]);
    }

    public function exists($key)
    {
        $exists = $this->redisService->exists($key);

        return response()->json(['key' => $key, 'exists' => $exists]);
    }

    public function flushAll()
    {
        $this->redisService->flushAll();

        return response()->json(['message' => 'All Redis keys flushed.']);
    }

    public function deleteByPattern(Request $request)
    {
        $pattern = $request->input('pattern');

        if (!$pattern) {
            return response()->json(['error' => 'Pattern is required.'], 400);
        }

        $deletedCount = $this->redisService->deleteKeysByPattern($pattern);

        return response()->json([
            'message' => "$deletedCount key(s) deleted for pattern '$pattern'."
        ]);
    }

    public function handleWithCache()
    {
        $result = $this->redisService->handleWithCache('sample_key', function () {
            // Simulasi data dari database atau proses berat
            return ['data' => 'This is cached data.', 'time' => now()];
        }, 120); // TTL = 120 detik

        return response()->json($result);
    }
}
