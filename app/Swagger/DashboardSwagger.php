<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class DashboardSwagger
{
    #[OA\Schema(
        schema: "Dashboard",
        type: "object",
        properties: [
            new OA\Property(property: "total_laporan", type: "integer", example: 10),
            new OA\Property(property: "total_event", type: "integer", example: 5),
            new OA\Property(property: "total_permintaan", type: "integer", example: 8),

            new OA\Property(property: "ayam_mati", type: "integer", example: 12),
            new OA\Property(property: "ayam_hidup", type: "integer", example: 150),

            new OA\Property(property: "permintaan_pending", type: "integer", example: 3),
            new OA\Property(property: "event_pending", type: "integer", example: 2),
        ]
    )]
    public function schema() {}

    #[OA\Get(
        path: "/api/dashboard",
        tags: ["Dashboard"],
        summary: "Ambil data dashboard",
        description: "Menampilkan ringkasan data laporan, event, dan permintaan",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil",
                content: new OA\JsonContent(ref: "#/components/schemas/Dashboard")
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index() {}
}