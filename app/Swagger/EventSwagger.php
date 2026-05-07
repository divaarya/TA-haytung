<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class EventSwagger
{
    #[OA\Schema(
        schema: "Event",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "role", type: "string", example: "kandang"),
            new OA\Property(property: "nama_kegiatan", type: "string", example: "Panen Ayam"),
            new OA\Property(property: "deskripsi", type: "string", nullable: true, example: "Panen periode Mei"),
            new OA\Property(property: "jumlah", type: "integer", nullable: true, example: 100),
            new OA\Property(property: "status", type: "string", enum: ["pending","acc","reject"], example: "pending"),
            new OA\Property(property: "tanggal", type: "string", format: "date", example: "2026-05-01"),
        ]
    )]

    #[OA\Schema(
        schema: "EventRequest",
        required: ["nama_kegiatan", "tanggal", "role"],
        properties: [
            new OA\Property(property: "nama_kegiatan", type: "string", example: "Panen Ayam"),
            new OA\Property(property: "deskripsi", type: "string", nullable: true, example: "Panen periode Mei"),
            new OA\Property(property: "jumlah", type: "integer", nullable: true, example: 100),
            new OA\Property(property: "tanggal", type: "string", format: "date", example: "2026-05-01"),
            new OA\Property(property: "role", type: "string", enum: ["kandang","gudang","reseller"], example: "kandang"),
        ]
    )]

    public function schema() {}

    // ================= GET ALL =================
    #[OA\Get(
        path: "/api/event",
        tags: ["Event"],
        summary: "Ambil semua event",
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
                            items: new OA\Items(ref: "#/components/schemas/Event")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index() {}

    // ================= STORE =================
    #[OA\Post(
        path: "/api/event",
        tags: ["Event"],
        summary: "Buat event (Admin only)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/EventRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Event berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Event berhasil dibuat"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Event"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store() {}

    // ================= SHOW =================
    #[OA\Get(
        path: "/api/event/{id}",
        tags: ["Event"],
        summary: "Detail event",
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
                        new OA\Property(property: "data", ref: "#/components/schemas/Event"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses ditolak"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function show() {}

    // ================= UPDATE =================
    #[OA\Put(
        path: "/api/event/{id}",
        tags: ["Event"],
        summary: "Update event (Admin only)",
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
            content: new OA\JsonContent(ref: "#/components/schemas/EventRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Event berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Event berhasil diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Event"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function update() {}

    // ================= UPDATE STATUS =================
    #[OA\Put(
        path: "/api/event/{id}/status",
        tags: ["Event"],
        summary: "Update status event (Admin only)",
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
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["pending","acc","reject"], example: "acc"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status event diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Status event diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Event"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function updateStatus() {}

    // ================= DELETE =================
    #[OA\Delete(
        path: "/api/event/{id}",
        tags: ["Event"],
        summary: "Hapus event (Admin only)",
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
                description: "Event dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Event dihapus"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Tidak ditemukan"),
        ]
    )]
    public function destroy() {}
}