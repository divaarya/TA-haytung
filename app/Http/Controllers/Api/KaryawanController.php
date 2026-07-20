<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        return response()->json(
            Karyawan::all()
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
            'role' => 'required|in:gudang,kandang,reseller',
            'status' => 'required|in:aktif,cuti',
            'nama_usaha' => 'nullable|string|max:255',
            'alamat_usaha' => 'nullable|string',
            'jenis_usaha' => 'nullable|string|max:255',
        ]);

        $karyawan = Karyawan::create($data);

        return response()->json([
            'message'=>'Data berhasil ditambah',
            'data'=>$karyawan
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Karyawan::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $data = $request->validate([
            'nama_lengkap'=>'sometimes',
            'nama_panggilan'=>'sometimes',
            'tempat_lahir'=>'sometimes',
            'tanggal_lahir' => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_bergabung' => 'sometimes|date',
            'role' => 'sometimes|in:gudang,kandang,reseller',
            'status' => 'sometimes|in:aktif,cuti',
            'nama_usaha' => 'sometimes|nullable',
            'alamat_usaha' => 'sometimes|nullable',
            'jenis_usaha' => 'sometimes|nullable',
        ]);

        $karyawan->update($data);

        return response()->json([
            'message'=>'Data berhasil diupdate',
            'data'=>$karyawan
        ]);
    }

    public function destroy($id)
    {
        Karyawan::destroy($id);

        return response()->json([
            'message'=>'Data berhasil dihapus'
        ]);
    }
}