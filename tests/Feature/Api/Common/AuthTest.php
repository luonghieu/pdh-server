<?php

namespace Tests\Feature\Api\Common;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $token;
    public $headers;
    public $faker;

    public function setup()
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();

        $this->user = factory(User::class)->create();

        $credentials = ['email' => $this->user->email, 'password' => '123456789'];
        $this->token = \JWTAuth::attempt($credentials);
        $this->headers = ['Authorization' => "Bearer $this->token"];
    }

    public function test_login_succeed()
    {
        $response = $this->call('POST', 'api/v1/auth/login', ['email' => $this->user->email, 'password' => '123456789',
            'type' => $this->user->type]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ]);
    }

    public function test_login_validate_minleng_wrong()
    {
        $response = $this->call('POST', 'api/v1/auth/login', ['email' => $this->user->email, 'password' => '1235',
            'type' => $this->user->type]);

        $response->assertStatus(400);
    }

    public function test_login_validate_required_wrong()
    {
        $response = $this->call('POST', 'api/v1/auth/login', []);

        $response->assertStatus(400);
    }

    public function test_login_validate_type_wrong_format()
    {
        $response = $this->call('POST', 'api/v1/auth/login', ['email' => $this->user->email, 'password' => '123456789',
            'type' => 'abc']);

        $response->assertStatus(400);
    }

    public function test_login_validate_email_wrong_format()
    {
        $response = $this->call('POST', 'api/v1/auth/login', ['email' => 'abc', 'password' => '123456789',
            'type' => $this->user->type]);

        $response->assertStatus(400);
    }

    public function test_login_failed()
    {
        $response = $this->call('POST', 'api/v1/auth/login', ['email' => $this->user->email, 'password' => 'wrong_password_foo',
            'type' => $this->user->type]);

        $response->assertStatus(401);
    }

    public function test_log_out()
    {
        $response = $this->get('api/v1/auth/logout', $this->headers);
        $response->assertStatus(200);
    }

    public function test_log_out_failed()
    {
        $response = $this->get('api/v1/auth/logout');
        $response->assertStatus(500);
    }

    public function test_reset_token()
    {
        $response = $this->post('api/v1/auth/refresh', [], $this->headers);

        $response->assertStatus(200);
    }

    public function test_reset_token_failed()
    {
        $response = $this->post('api/v1/auth/refresh', []);

        $response->assertStatus(500);
    }
}
