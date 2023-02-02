<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulAuthentication(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email'     => $user->email,
            'password'  => User::FACTORY_PASSWORD,
        ]);

        $response->assertSuccessful()
            ->assertJson([
                'auth_token' => true,
            ])
            ->assertJsonPath('user.email', $user->email);
    }


    public function testEmailDoesNotExists(): void
    {
        $response = $this->postJson('/api/login', [
            'email'     => 'example@example.com',
            'password'  => User::FACTORY_PASSWORD,
        ]);

        $response->assertNotFound();
    }


    public function testPasswordInvalid(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email'     => $user->email,
            'password'  => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }


    public function testPasswordDoesNotMatch(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email'     => $user->email,
            'password'  => 'password',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }
}
