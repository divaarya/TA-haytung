<?php

namespace App;

/**
 * @OA\Info(
 *     title="API Peternakan",
 *     version="1.0.0"
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000"
 * )
 * 
 * @OA\Get(
 *     path="/api/test",
 *     summary="Test endpoint",
 *     @OA\Response(response=200, description="OK")
 * )
 */
class OpenApi {}