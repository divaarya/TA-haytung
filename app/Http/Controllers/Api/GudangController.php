<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Gudang::orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:gudangs,nama',
        ]);

        $gudang = Gudang::create($validated);

        return response()->json([
            'message' => 'Gudang berhasil ditambahkan',
            'data' => $gudang,
        ], 201);
    }
}
