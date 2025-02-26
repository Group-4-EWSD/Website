<?php
// app/Swagger/SwaggerConfig.php
namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     description="JWT Authorization"
 * )
 */
class SwaggerConfig
{
    // You can keep this empty, as it's just for annotations.
}
