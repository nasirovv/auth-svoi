<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param             $data
     * @param string|null $message
     * @param int         $code
     * @return JsonResponse
     */
    public function render_json($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json(
            [
                'code'    => $code,
                'message' => is_null($message) ? trans('main.successfully') : $message,
                'data'    => $data,
            ],
            $code
        );
    }
}
