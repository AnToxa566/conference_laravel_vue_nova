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


    public function testSuccessfulLogin(): void
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


    public function testLoginWhenEmailDoesNotExists(): void
    {
        $response = $this->postJson('/api/login', [
            'email'     => 'example@example.com',
            'password'  => User::FACTORY_PASSWORD,
        ]);

        $response->assertNotFound();
    }


    public function testLoginWithInvalidPassword(): void
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


    public function testLoginWhenPasswordDoesNotMatch(): void
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
