<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    //login mahasiswa dengan nim & password
    public function login(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'password' => 'required'
        ]);
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

            if (!$mahasiswa || !Hash::check($request->password, $mahasiswa->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'nim dan password salah',
                ], 400);
            }

            $token = $mahasiswa->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil login',
                'data' => ['token' => 'Bearer ' . $token]
            ], 200);
        }, 'Login');
    }

    // Ambil semua data mahasiswa
    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {

            return response()->json([
                'success' => true,
                'message' => 'List semua mahasiswa',
                'data' => Mahasiswa::all()
            ], 200);
        }, 'List Mahasiswa');
    }

    // Simpan mahasiswa baru
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa,nim|max:15',
            'nama' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'password' => 'required'
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'nim.max' => 'NIM maksimal 15 karakter.',
            'nama.required' => 'Nama wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin hanya boleh L (Laki-laki) atau P (Perempuan).',
            'password.required' => 'Password wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = [
                'nim' => $request->nim,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'password' => Hash::make($request->password)
            ];

            $mahasiswa = Mahasiswa::create($data);

            $this->transactionService->handleWithLogDB('Create Mahasiswa', 'mahasiswa', $mahasiswa->nim, $data);

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil ditambahkan!',
                'data' => $mahasiswa
            ], 201);
        }, 'Create Mahasiswa');
    }

    // Tampilkan mahasiswa berdasarkan NIM
    public function show($nim)
    {
        return $this->transactionService->handleWithTransaction(function () use ($nim) {
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Detail mahasiswa',
                'data' => $mahasiswa
            ], 200);

        }, 'Detail Mahasiswa');
    }

    // Update mahasiswa
    public function update(Request $request, $nim)
    {
        $request->validate([
            'nama' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'password' => 'required'
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin hanya boleh L (Laki-laki) atau P (Perempuan).',
            'password.required' => 'Password wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($nim, $request) {
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'password' => Hash::make($request->password)
            ];

            $mahasiswa->update($data);

            $this->transactionService->handleWithLogDB('Update Mahasiswa', 'mahasiswa', $nim, $data);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui!'
            ], 200);

        }, 'Update Mahasiswa');
    }

    // Hapus mahasiswa
    public function destroy($nim)
    {
        return $this->transactionService->handleWithTransaction(function () use ($nim) {
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'
                ], 404);
            }
            //log data mahaiswa sebelum di hapus
            $this->transactionService->handleWithLogDB('Delete Mahasiswa', 'mahasiswa', $nim, $mahasiswa);

            $mahasiswa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil dihapus!'
            ], 200);

        },'Delete Mahasiswa');
    }
}
