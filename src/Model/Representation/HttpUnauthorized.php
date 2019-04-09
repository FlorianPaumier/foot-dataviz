<?php

namespace App\Model\Representation;

use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="401",
 *     description="JWT Token not found",
 * )
 */
class HttpUnauthorized
{

}