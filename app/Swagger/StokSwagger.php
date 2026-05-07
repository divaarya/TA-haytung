<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class StokSwagger
{
    #[OA\Schema(
        schema: "Stok",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "jenis", type: "string", enum: ["whole","parting"], example: "whole"),
            new OA\Property(property: "berat_per_item", type: "number", example: 1.5),
            new OA\Property(property: "jumlah_stok", type: "integer", example: 100),
            new OA\Property(property: "estimasi_total_berat", type: "number", example: 150),
            new OA\Property(property: "tanggal_update", type: "string", format: "date", example: "2026-05-06"),
            new OA\Property(property: "status", type: "string", enum: ["aman","menipis","habis"], example: "aman"),
        ]
    )]

    #[OA\Schema(
        schema: "StokRequest",
        required: [
            "jenis",
            "berat_per_item",
            "jumlah_stok",
            "estimasi_total_berat",
            "tanggal_update",
            "status"
        ],
        properties: [
            new OA\Property(property: "jenis", type: "string", enum: ["whole","parting"], example: "whole"),
            new OA\Property(property: "berat_per_item", type: "number", example: 1.5),
            new OA\Property(property: "jumlah_stok", type: "integer", example: 100),
            new OA\Property(property: "estimasi_total_berat", type: "number", example: 150),
            new OA\Property(property: "tanggal_update", type: "string", format: "date", example: "2026-05-06"),
            new OA\Property(property: "status", type: "string", enum: ["aman","menipis","habis"], example: "aman"),
        ]
    )]

    public function schema() {}

    // ================= GET ALL =================
    #[OA\Get(
        path: "/api/stok",
        tags: ["Stok"],
        summary: "Ambil semua stok",
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
                            items: new OA\Items(ref: "#/components/schemas/Stok")
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
        path: "/api/stok/{id}",
        tags: ["Stok"],
        summary: "Detail stok",
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
                        new OA\Property(property: "data", ref: "#/components/schemas/Stok"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function show() {}

    // ================= STORE =================
    #[OA\Post(
        path: "/api/stok",
        tags: ["Stok"],
        summary: "Tambah stok (khusus gudang)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StokRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Stok berhasil ditambahkan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Stok berhasil ditambahkan"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Stok"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store() {}

    // ================= UPDATE =================
    #[OA\Put(
        path: "/api/stok/{id}",
        tags: ["Stok"],
        summary: "Update stok (khusus gudang)",
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
            content: new OA\JsonContent(ref: "#/components/schemas/StokRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Stok berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Stok berhasil diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Stok"),
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
        path: "/api/stok/{id}",
        tags: ["Stok"],
        summary: "Hapus stok (gudang & admin)",
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
                description: "Stok berhasil dihapus",
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