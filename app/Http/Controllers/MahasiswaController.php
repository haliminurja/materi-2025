<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{

    //login mahasiswa dengan nim & password
    public function login(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'password' => 'required'
        ]);

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
            'data' => ['token' => 'Bearer '.$token]
        ], 200);
    }

    // Ambil semua data mahasiswa
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List semua mahasiswa',
            'data' => Mahasiswa::all()
        ], 200);
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

        try {
            $mahasiswa = Mahasiswa::create([
                'nim' => $request->nim,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil ditambahkan!',
                'data' => $mahasiswa
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, mahasiswa gagal ditambahkan!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Tampilkan mahasiswa berdasarkan NIM
    public function show($nim)
    {
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
    }

    // Update mahasiswa
    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'
            ], 404);
        }

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

        try {

            $mahasiswa->update([
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, data mahasiswa gagal diperbarui!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus mahasiswa
    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'
            ], 404);
        }

        try {
            $mahasiswa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil dihapus!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, mahasiswa gagal dihapus!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
