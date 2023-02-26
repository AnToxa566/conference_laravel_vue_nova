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
        $user = User::factory()->create()->toArray();
        $user['password'] = User::FACTORY_PASSWORD;

        $this
            ->postJson('/api/login', $user)
            ->assertSuccessful()
            ->assertJson([
                'user' => true,
                'auth_token' => true,
            ])
            ->assertJsonPath('user.email', $user['email']);
    }


    public function testLoginWhenEmailDoesNotExists(): void
    {
        $this
            ->postJson('/api/login', [
                'email'     => 'example@example.com',
                'password'  => User::FACTORY_PASSWORD,
            ])
            ->assertNotFound();
    }


    public function testLoginWithInvalidPassword(): void
    {
        $this
            ->postJson('/api/login', [
                'email'     => (User::factory()->create())->email,
                'password'  => 'password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }


    public function testAdminTryingToLogin(): void
    {
        $this
            ->postJson('/api/login', [
                'email'     => (User::factory()->admin()->create())->email,
                'password'  => User::FACTORY_PASSWORD,
            ])
            ->assertForbidden();
    }
}
