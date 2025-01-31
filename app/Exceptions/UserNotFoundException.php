<?php
/**
 * exception for when user not found
 * @author Hojjat koochak zadeh
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserNotFoundException extends Exception
{
    /**
     * @param mixed $message message return to client
     * @param mixed $code http status code
     */
    public function __construct(
        protected $message = "User not found",
        protected $code = 404
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
