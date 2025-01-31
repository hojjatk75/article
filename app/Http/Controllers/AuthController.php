<?php
/**
 * controller for Auth
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Controllers;

use App\Exceptions\LoginFailedException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\VerificationCodeExpiredException;
use App\Exceptions\VerificationCodeInvalidException;
use App\Http\Requests\Auth\CompleteRegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ReturnErrorMessageTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    use ReturnErrorMessageTrait;

    /**
     * @param AuthService $authService
     */
    public function __construct(
        protected AuthService $authService
    )
    {}

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->get('email'));
    }

    /**
     * @param VerifyCodeRequest $request
     * @return JsonResponse
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function verify(VerifyCodeRequest $request): JsonResponse
    {
        $verify = $this->authService->verify($request->get('email'), $request->get('code'));

        if($verify) {
            return response()->json([
                'message' => 'Email confirmed successfully!',
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param CompleteRegisterRequest $request
     * @return JsonResponse
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function completeRegister(CompleteRegisterRequest $request): JsonResponse
    {
        $user = $this->authService->completeRegister($request->all());

        if($user) {
            return response()->json([
                'message' => 'Profile updated successfully!',
                'user' => new UserResource($user)
            ]);
        }


        return $this->errorResponse();
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws LoginFailedException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->only(['email', 'password']));
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws UserNotFoundException
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->forgotPassword($request->get('email'));
        return response()->json([
            'message' => 'Code sent to your email!',
        ]);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $reset_password = $this->authService->resetPassword($request->all());
        if($reset_password){
            return response()->json([
                'message' => 'Password reset successfully!',
            ]);
        }

        return $this->errorResponse();
    }
}
