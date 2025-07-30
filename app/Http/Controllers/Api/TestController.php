<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class testController extends Controller
{
    public function gg(): JsonResponse
    {
        return response()->json(
            ['gg' => 'yes']
        );
    }
}
