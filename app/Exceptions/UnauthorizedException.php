<?php
/**
 * exception for unauthorized users
 * @author Hojjat koochak zadeh
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    /**
     * @param mixed $message message return to client
     * @param mixed $code http status code
     */
    public function __construct(
        protected $message = "Unauthorized action",
        protected $code = 403
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
