<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class LaporanGudangSwagger
{
    #[OA\Schema(
        schema: "LaporanGudang",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "tanggal_mulai", type: "string", format: "date", example: "2026-05-01"),
            new OA\Property(property: "tanggal_selesai", type: "string", format: "date", example: "2026-05-05"),
            new OA\Property(property: "stok_awal", type: "integer", example: 100),
            new OA\Property(property: "stok_masuk", type: "integer", example: 50),
            new OA\Property(property: "jumlah_daging_jual", type: "integer", example: 30),
            new OA\Property(property: "stok_akhir", type: "integer", example: 120),
            new OA\Property(property: "catatan", type: "string", nullable: true, example: "Stok aman"),
            new OA\Property(property: "foto", type: "string", nullable: true, example: "laporan_gudang/abc.jpg"),
        ]
    )]

    public function schema() {}

    // ================= GET ALL =================
    #[OA\Get(
        path: "/api/laporan-gudang",
        tags: ["Laporan Gudang"],
        summary: "Ambil semua laporan gudang",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/LaporanGudang")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index() {}

    // ================= SHOW =================
    #[OA\Get(
        path: "/api/laporan-gudang/{id}",
        tags: ["Laporan Gudang"],
        summary: "Detail laporan gudang",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanGudang"),
                        new OA\Property(property: "foto_url", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function show() {}

    // ================= STORE =================
    #[OA\Post(
        path: "/api/laporan-gudang",
        tags: ["Laporan Gudang"],
        summary: "Buat laporan gudang (khusus role gudang)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: [
                        "tanggal_mulai",
                        "tanggal_selesai",
                        "stok_awal",
                        "stok_masuk",
                        "jumlah_daging_jual"
                    ],
                    properties: [
                        new OA\Property(property: "tanggal_mulai", type: "string", format: "date", example: "2026-05-01"),
                        new OA\Property(property: "tanggal_selesai", type: "string", format: "date", example: "2026-05-05"),
                        new OA\Property(property: "stok_awal", type: "integer", example: 100),
                        new OA\Property(property: "stok_masuk", type: "integer", example: 50),
                        new OA\Property(property: "jumlah_daging_jual", type: "integer", example: 30),
                        new OA\Property(property: "stok_akhir", type: "integer", nullable: true, example: 120),
                        new OA\Property(property: "catatan", type: "string", nullable: true, example: "Stok aman"),
                        new OA\Property(property: "foto", type: "string", format: "binary", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Laporan gudang berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanGudang"),
                        new OA\Property(property: "foto_url", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store() {}

    // ================= UPDATE =================
    #[OA\Post(
        path: "/api/laporan-gudang/{id}",
        tags: ["Laporan Gudang"],
        summary: "Update laporan gudang",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "tanggal_mulai", type: "string", format: "date"),
                        new OA\Property(property: "tanggal_selesai", type: "string", format: "date"),
                        new OA\Property(property: "stok_awal", type: "integer"),
                        new OA\Property(property: "stok_masuk", type: "integer"),
                        new OA\Property(property: "jumlah_daging_jual", type: "integer"),
                        new OA\Property(property: "stok_akhir", type: "integer", nullable: true),
                        new OA\Property(property: "catatan", type: "string", nullable: true),
                        new OA\Property(property: "foto", type: "string", format: "binary", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan gudang berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanGudang"),
                        new OA\Property(property: "foto_url", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function update() {}

    // ================= DELETE =================
    #[OA\Delete(
        path: "/api/laporan-gudang/{id}",
        tags: ["Laporan Gudang"],
        summary: "Hapus laporan gudang",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan gudang berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function destroy() {}
}