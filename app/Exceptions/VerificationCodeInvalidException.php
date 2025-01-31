<?php
/**
 * exception for when verification code invalid
 * @author Hojjat koochak zadeh
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class VerificationCodeInvalidException extends Exception
{
    /**
     * @param mixed $message message return to client
     * @param mixed $code http status code
     */
    public function __construct(
        protected $message = "Code is invalid",
        protected $code = 401
    )
    {
        parent::__construct($message, $code);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
