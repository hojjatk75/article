<?php

namespace Tests\Unit;

use App\Services\AuthService;
use App\Models\User;
use App\Jobs\SendUserVerifyCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\{
    LoginFailedException,
    UnauthorizedException,
    UserNotFoundException,
    VerificationCodeExpiredException,
    VerificationCodeInvalidException
};
use Illuminate\Support\Facades\{
    Hash,
    Auth,
    Response,
};
use Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    protected $authService;

    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    #[Test]
    public function test_register_success()
    {
        $email = 'test@example.com';

        // Mock a user instance
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('first')->andReturnNull();
        $user->shouldReceive('create')->andReturn($user);

        $response = $this->authService->register($email);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('User registered and verify code sent to your email!', $response->getData()->message);
    }

    #[Test]
    public function test_register_failed_duplicated_email()
    {
        $email = 'test@example.com';
        $new_user = User::create(['email' => $email, 'password' => '1234356']);
        $new_user->markEmailAsVerified();

        // Mock a user instance
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('first')->andReturn($user);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);

        $response = $this->authService->register($email);

        $this->assertEquals(409, $response->status());
        $this->assertEquals('The email has already been taken.', $response->getData()->message);
    }

    #[Test]
    public function test_verify_success()
    {
        $email = 'test@example.com';
        $code = '123456';
        $new_user = User::create(['email' => $email, 'password' => '1234356', 'verify_code' => Hash::make($code)]);

        // Mock a user instance
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('first')->andReturn($new_user);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('verifyCodeIsExpired')->andReturn(false);

        $result = $this->authService->verify($email, $code);

        $this->assertTrue($result);
    }

    #[Test]
    public function test_verify_failed_not_fount()
    {
        $this->expectException(UserNotFoundException::class);

        $email = 'nonexistent@example.com';
        $code = '123456';

        $this->authService->verify($email, $code);
    }

    #[Test]
    public function test_verify_failed()
    {
        $this->expectException(VerificationCodeInvalidException::class);
        $email = 'test@example.com';
        $code = 'invalidcode';

        $credentials = ['email' => $email, 'password' => 'password'];

        $new_user = User::create($credentials);

        // Mock a user instance
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('first')->andReturn($new_user);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('verifyCodeIsExpired')->andReturn(false);
        $user->shouldReceive('verify_code')->andReturn(Hash::make('correctcode'));

        $this->authService->verify($email, $code);
    }

    #[Test]
    public function test_login_success()
    {
        $credentials = ['email' => 'test@example.com', 'password' => 'password'];

        $new_user = User::create($credentials);
        $new_user->markEmailAsVerified();
        // Mock a user instance and authentication
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('verifiedEmail')->andReturnSelf();
        $user->shouldReceive('first')->andReturn($new_user);

        Auth::shouldReceive('attempt')->with($credentials)->andReturn(true);
        Auth::shouldReceive('user')->andReturn($new_user);

        $response = $this->authService->login($credentials);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('token', $response->getData('token'));
    }

    #[Test]
    public function test_login_failed()
    {
        $this->expectException(LoginFailedException::class);
        $credentials = ['email' => 'test@example.com', 'password' => 'wrongpassword'];

        // Mock a user instance and authentication failure
        $user = Mockery::mock(User::class);
        $user->shouldReceive('where')->andReturnSelf();
        $user->shouldReceive('verifiedEmail')->andReturnSelf();
        $user->shouldReceive('first')->andReturn($user);

        Auth::shouldReceive('attempt')->with($credentials)->andReturn(false);
        $this->authService->login($credentials);
    }
}
