<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        return response()->json(
            Karyawan::with('user')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_bergabung' => 'required|date',
            'role' => 'required|in:gudang,kandang,reseller,admin',
            'gudang' => 'nullable|exists:gudangs,nama',
            'status' => 'required|in:aktif,cuti',
            'nama_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string',
            'jenis_usaha' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('users', 'public')
            : null;

        $karyawan = DB::transaction(function () use ($data, $fotoPath) {
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'status' => $data['status'],
                'no_hp' => $data['no_hp'] ?? null,
                'foto' => $fotoPath,
            ]);

            $karyawanData = $data;
            unset($karyawanData['email'], $karyawanData['password'], $karyawanData['no_hp'], $karyawanData['foto']);
            $karyawanData['user_id'] = $user->id;

            return Karyawan::create($karyawanData);
        });

        return response()->json([
            'message' => 'Data berhasil ditambah',
            'data' => $karyawan->load('user'),
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Karyawan::with('user')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $data = $request->validate([
            'nama_lengkap' => 'sometimes|string|max:255',
            'nama_panggilan' => 'sometimes|string|max:100',
            'tempat_lahir' => 'sometimes|string|max:100',
            'tanggal_lahir' => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_bergabung' => 'sometimes|date',
            'role' => 'sometimes|in:gudang,kandang,reseller,admin',
            'gudang' => 'sometimes|nullable|exists:gudangs,nama',
            'status' => 'sometimes|in:aktif,cuti',
            'nama_usaha' => 'sometimes|nullable|string|max:255',
            'alamat_usaha' => 'sometimes|nullable|string',
            'jenis_usaha' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . optional($karyawan->user)->id,
            'password' => 'sometimes|nullable|string|min:6',
            'no_hp' => 'sometimes|nullable|string|max:20',
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('users', 'public')
            : null;

        DB::transaction(function () use ($karyawan, $data, $fotoPath) {
            $karyawanData = $data;
            unset($karyawanData['email'], $karyawanData['password'], $karyawanData['no_hp'], $karyawanData['foto']);
            $karyawan->update($karyawanData);

            if ($karyawan->user) {
                $userData = [];
                if (isset($data['nama_lengkap'])) $userData['name'] = $data['nama_lengkap'];
                if (isset($data['email'])) $userData['email'] = $data['email'];
                if (isset($data['role'])) $userData['role'] = $data['role'];
                if (isset($data['status'])) $userData['status'] = $data['status'];
                if (array_key_exists('no_hp', $data)) $userData['no_hp'] = $data['no_hp'];
                if (!empty($data['password'])) $userData['password'] = Hash::make($data['password']);
                if ($fotoPath) $userData['foto'] = $fotoPath;

                if (!empty($userData)) {
                    $karyawan->user->update($userData);
                }
            }
        });

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $karyawan->fresh('user'),
        ]);
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if ($karyawan->user) {
            // cascadeOnDelete pada foreign key ikut menghapus baris karyawan ini
            $karyawan->user->delete();
        } else {
            $karyawan->delete();
        }

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
