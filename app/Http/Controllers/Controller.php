<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Swagger
 * (
 *   schemes={"http"},
 *   host="promindsl.ru",
 *   basePath="/api",
 *   @OA\Info
 *   (
 *     title="Letovo test API",
 *     version="1.0.0"
 *   )
 * )
 *
 *
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
