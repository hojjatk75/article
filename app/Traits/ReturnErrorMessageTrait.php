<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait ReturnErrorMessageTrait
{
    /**
     * @return JsonResponse
     */
    public function errorResponse(): JsonResponse
    {
        return Response::json([
            'message' => __('An error occurred!')
        ], 500);
    }
}
