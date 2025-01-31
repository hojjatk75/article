<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Services;

use App\Events\Auth\UserRegistered;
use App\Exceptions\LoginFailedException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\VerificationCodeExpiredException;
use App\Exceptions\VerificationCodeInvalidException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendUserVerifyCode;
use App\Models\User;
use App\Traits\ReturnErrorMessageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AuthService
{
    use ReturnErrorMessageTrait;

    /**
     * @param $email
     * @return JsonResponse
     */
    public function register($email) : JsonResponse
    {
        $user = User::where([['email', $email]])->first();
        if(! empty($user)){
            if($user->hasVerifiedEmail()){
                return Response::json([
                    'message' => __('The email has already been taken.')
                ], 409);
            }else{
                if($user->verifyCodeIsExpired()) {
                    SendUserVerifyCode::dispatch($user);
                    return Response::json([
                        'message' => __('Verify code sent to your email!')
                    ], 201);
                }else{
                    return Response::json([
                        'message' => __('You can get new verify code in :seconds seconds', ['seconds' => ceil(now()->diffInSeconds($user->verify_code_expire_at))])
                    ], 429);
                }
            }
        }
        $user = User::create([
            'email' => $email,
            'password' => Str::random()
        ]);

        if($user->id){
            UserRegistered::dispatch($user);
            return Response::json([
                'message' => __('User registered and verify code sent to your email!')
            ], 201);
        }

        return $this->errorResponse();
    }

    /**
     * @param $email
     * @param $code
     * @return bool
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function verify($email, $code): bool
    {
        $user = User::where([['email', $email]])->first();
        if(empty($user)){
            throw new UserNotFoundException();
        }

        if($user->hasVerifiedEmail()){
            throw new UnauthorizedException();
        }

        if(! $user->verifyCodeIsExpired()){
            if(Hash::check($code, $user->verify_code)){
                $user->markEmailAsVerified();
            }else{
                throw new VerificationCodeInvalidException();
            }
        }else{
            SendUserVerifyCode::dispatch($user);
            throw new VerificationCodeExpiredException();
        }

        return true;
    }

    /**
     * @param $validated_data
     * @return bool|User
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function completeRegister($validated_data): bool|User
    {
        $user = User::where([['email', $validated_data['email']]])->first();
        if(empty($user)){
            throw new UserNotFoundException();
        }

        if(! $user->verifyCodeIsExpired()){
            if(Hash::check($validated_data['code'], $user->verify_code)){
                $result = $user->update([
                    'name' => $validated_data['name'],
                    'family' => $validated_data['family'],
                    'password' => $validated_data['password'],
                ]);
                if($result)
                    return $user;

                return false;
            }else{
                throw new VerificationCodeInvalidException();
            }
        }else{
            SendUserVerifyCode::dispatch($user);
            throw new VerificationCodeExpiredException();
        }
    }

    /**
     * @param $credentials
     * @return JsonResponse
     * @throws LoginFailedException
     */
    public function login($credentials) : JsonResponse
    {
        $user = User::where([['email', $credentials['email']]])->verifiedEmail()->first();
        if(! empty($user)){
            if (Auth::attempt($credentials)) {
                return Response::json([
                    'message' => 'Successful login!',
                    'user' => new UserResource(Auth::user()),
                    'token' => Auth::user()->createToken('API_Token')->plainTextToken
                ]);
            }
        }

        throw new LoginFailedException();
    }

    /**
     * @param $email
     * @return void
     * @throws UserNotFoundException
     */
    public function forgotPassword($email): void
    {
        $user = User::where([['email', $email]])->first();
        if(empty($user)){
            throw new UserNotFoundException();
        }

        SendUserVerifyCode::dispatch($user);
    }

    /**
     * @param $validated_data
     * @return bool
     * @throws UserNotFoundException
     * @throws VerificationCodeExpiredException
     * @throws VerificationCodeInvalidException
     */
    public function resetPassword($validated_data) : bool
    {
        $user = User::where([['email', $validated_data['email']]])->first();
        if(empty($user)){
            throw new UserNotFoundException();
        }

        if(! $user->verifyCodeIsExpired()){
            if(Hash::check($validated_data['code'], $user->verify_code)){
                return $user->update([
                    'password' => $validated_data['password'],
                ]);
            }else{
                throw new VerificationCodeInvalidException();
            }
        }else{
            SendUserVerifyCode::dispatch($user);
            throw new VerificationCodeExpiredException();
        }
    }
}
