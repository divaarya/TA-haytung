<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class AuthSwagger
{
    #[OA\Schema(
        schema: "User",
        type: "object",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "Arya"),
            new OA\Property(property: "email", type: "string", example: "arya@mail.com"),
            new OA\Property(property: "role", type: "string", example: "kandang"),
            new OA\Property(property: "foto", type: "string", nullable: true, example: "users/abc.jpg"),
        ]
    )]

    public function schema() {}

    // ================= REGISTER =================
    #[OA\Post(
        path: "/api/register",
        tags: ["Auth"],
        summary: "Register user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "email", "password", "role"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Arya"),
                        new OA\Property(property: "email", type: "string", example: "arya@mail.com"),
                        new OA\Property(property: "password", type: "string", example: "password123"),
                        new OA\Property(property: "role", type: "string", enum: ["admin","kandang","gudang","reseller"], example: "kandang"),
                        new OA\Property(
                            property: "foto",
                            type: "string",
                            format: "binary",
                            nullable: true
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User berhasil dibuat"),
                        new OA\Property(property: "data", ref: "#/components/schemas/User"),
                        new OA\Property(property: "foto_url", type: "string", nullable: true, example: "http://localhost/storage/users/abc.jpg"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function register() {}

    // ================= LOGIN =================
    #[OA\Post(
        path: "/api/login",
        tags: ["Auth"],
        summary: "Login user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "arya@mail.com"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login berhasil"),
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "data", ref: "#/components/schemas/User"),
                        new OA\Property(property: "role", type: "string", example: "kandang"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Email atau password salah"),
        ]
    )]
    public function login() {}

    // ================= LOGOUT =================
    #[OA\Post(
        path: "/api/logout",
        tags: ["Auth"],
        summary: "Logout user",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout berhasil"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function logout() {}

    // ================= UPDATE PROFILE =================
    #[OA\Post(
        path: "/api/update-profile",
        tags: ["Auth"],
        summary: "Update profile user",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Arya Update"),
                        new OA\Property(
                            property: "foto",
                            type: "string",
                            format: "binary",
                            nullable: true
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile berhasil diupdate",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Profile berhasil diupdate"),
                        new OA\Property(property: "data", ref: "#/components/schemas/User"),
                        new OA\Property(property: "foto_url", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function updateProfile() {}
}