<?php
/**
 * exception for rate limit
 * @author Hojjat koochak zadeh
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class TooManyAttemptException extends Exception
{
    /**
     * @param mixed $message message return to client
     * @param mixed $code http status code
     */
    public function __construct(
        protected $message = "Too many attempt",
        protected $code = 429
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
