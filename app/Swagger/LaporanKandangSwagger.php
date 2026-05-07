<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class LaporanKandangSwagger
{
    #[OA\Schema(
        schema: "LaporanKandang",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "tanggal_mulai", type: "string", format: "date", example: "2026-05-01"),
            new OA\Property(property: "tanggal_selesai", type: "string", format: "date", example: "2026-05-05"),
            new OA\Property(property: "jumlah_ayam_awal", type: "integer", example: 100),
            new OA\Property(property: "jumlah_ayam_mati", type: "integer", example: 5),
            new OA\Property(property: "umur_ayam", type: "integer", example: 30),
            new OA\Property(property: "rata_rata_bobot", type: "number", example: 1.5),
            new OA\Property(property: "catatan", type: "string", nullable: true, example: "Kondisi baik"),
            new OA\Property(property: "foto", type: "string", nullable: true, example: "laporan_kandang/abc.jpg"),
        ]
    )]

    public function schema() {}

    // ================= GET ALL =================
    #[OA\Get(
        path: "/api/laporan-kandang",
        tags: ["Laporan Kandang"],
        summary: "Ambil semua laporan kandang",
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
                            items: new OA\Items(ref: "#/components/schemas/LaporanKandang")
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
        path: "/api/laporan-kandang/{id}",
        tags: ["Laporan Kandang"],
        summary: "Detail laporan kandang",
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
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanKandang"),
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
        path: "/api/laporan-kandang",
        tags: ["Laporan Kandang"],
        summary: "Buat laporan kandang (khusus role kandang)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: [
                        "tanggal_mulai",
                        "tanggal_selesai",
                        "jumlah_ayam_awal",
                        "jumlah_ayam_mati",
                        "umur_ayam",
                        "rata_rata_bobot"
                    ],
                    properties: [
                        new OA\Property(property: "tanggal_mulai", type: "string", format: "date", example: "2026-05-01"),
                        new OA\Property(property: "tanggal_selesai", type: "string", format: "date", example: "2026-05-05"),
                        new OA\Property(property: "jumlah_ayam_awal", type: "integer", example: 100),
                        new OA\Property(property: "jumlah_ayam_mati", type: "integer", example: 5),
                        new OA\Property(property: "umur_ayam", type: "integer", example: 30),
                        new OA\Property(property: "rata_rata_bobot", type: "number", example: 1.5),
                        new OA\Property(property: "catatan", type: "string", nullable: true, example: "Kondisi baik"),
                        new OA\Property(property: "foto", type: "string", format: "binary", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Laporan kandang berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanKandang"),
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
        path: "/api/laporan-kandang/{id}",
        tags: ["Laporan Kandang"],
        summary: "Update laporan kandang",
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
                        new OA\Property(property: "jumlah_ayam_awal", type: "integer"),
                        new OA\Property(property: "jumlah_ayam_mati", type: "integer"),
                        new OA\Property(property: "umur_ayam", type: "integer"),
                        new OA\Property(property: "rata_rata_bobot", type: "number"),
                        new OA\Property(property: "catatan", type: "string", nullable: true),
                        new OA\Property(property: "foto", type: "string", format: "binary", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan kandang berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/LaporanKandang"),
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
        path: "/api/laporan-kandang/{id}",
        tags: ["Laporan Kandang"],
        summary: "Hapus laporan kandang",
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
                description: "Laporan kandang berhasil dihapus",
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