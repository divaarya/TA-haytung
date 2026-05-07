<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class PermintaanSwagger
{
    #[OA\Schema(
        schema: "Permintaan",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "nama_permintaan", type: "string", example: "Pakan Ayam"),
            new OA\Property(property: "tipe", type: "string", enum: ["barang", "dana"], example: "barang"),
            new OA\Property(property: "jumlah", type: "integer", nullable: true, example: 10),
            new OA\Property(property: "harga", type: "number", nullable: true, example: 150000),
            new OA\Property(property: "status", type: "string", enum: ["pending", "disetujui", "ditolak"], example: "pending"),
            new OA\Property(property: "alasan_tolak", type: "string", nullable: true, example: null),
            new OA\Property(property: "tanggal", type: "string", format: "date", example: "2026-04-20"),

            // sesuai controller (nested user)
            new OA\Property(
                property: "user",
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "role", type: "string", example: "admin"),
                ]
            ),
        ]
    )]

    #[OA\Schema(
        schema: "PermintaanRequest",
        required: ["nama_permintaan", "tipe", "tanggal"],
        properties: [
            new OA\Property(property: "nama_permintaan", type: "string", example: "Pakan Ayam"),
            new OA\Property(property: "tipe", type: "string", enum: ["barang", "dana"], example: "barang"),
            new OA\Property(property: "jumlah", type: "integer", nullable: true, example: 10),
            new OA\Property(property: "harga", type: "number", nullable: true, example: 150000),
            new OA\Property(property: "tanggal", type: "string", format: "date", example: "2026-04-20"),
        ]
    )]
    public function schema() {}

    #[OA\Get(
        path: "/api/permintaan",
        tags: ["Permintaan"],
        summary: "Ambil semua permintaan",
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
                            items: new OA\Items(ref: "#/components/schemas/Permintaan")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/permintaan",
        tags: ["Permintaan"],
        summary: "Buat permintaan baru",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/PermintaanRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Permintaan berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Permintaan berhasil dibuat"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Permintaan"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: "/api/permintaan/{id}",
        tags: ["Permintaan"],
        summary: "Detail permintaan",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Permintaan"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: "/api/permintaan/{id}",
        tags: ["Permintaan"],
        summary: "Update permintaan",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/PermintaanRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Permintaan berhasil diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Permintaan"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/permintaan/{id}",
        tags: ["Permintaan"],
        summary: "Hapus permintaan",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Permintaan berhasil dihapus"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function destroy() {}

    #[OA\Put(
        path: "/api/permintaan/{id}/status",
        tags: ["Permintaan"],
        summary: "Update status permintaan",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["pending", "disetujui", "ditolak"], example: "disetujui"),
                    new OA\Property(property: "alasan_tolak", type: "string", nullable: true, example: "Stok tidak tersedia"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Status berhasil diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Permintaan"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function updateStatus() {}
}