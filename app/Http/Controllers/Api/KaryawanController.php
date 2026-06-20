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
            'nama_lengkap'=>'required',
            'nama_panggilan'=>'required',
            'tempat_lahir'=>'required',
            'tanggal_lahir'=>'required|date',
            'jenis_kelamin'=>'required|in:L,P',
            'tanggal_bergabung'=>'required|date',
            'role'=>'required|in:gudang,kandang,reseller',
            'status'=>'required|in:aktif,cuti',
            'nama_usaha'=>'nullable',
            'alamat_usaha'=>'nullable',
            'jenis_usaha'=>'nullable'
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
            'tanggal_lahir'=>'date',
            'jenis_kelamin'=>'in:L,P',
            'tanggal_bergabung'=>'date',
            'role'=>'in:gudang,kandang,reseller',
            'status'=>'in:aktif,cuti',
            'nama_usaha'=>'nullable',
            'alamat_usaha'=>'nullable',
            'jenis_usaha'=>'nullable'
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