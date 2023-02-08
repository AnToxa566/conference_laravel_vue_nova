<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;


class RegisterTest extends TestCase
{
    use RefreshDatabase;


    protected function getUserDataToRegister($isInvalidPassword = false): array
    {
        $userData = User::factory()->make()->toArray();

        $invalidPassword = '123';

        $userData['password'] = $isInvalidPassword ? $invalidPassword : User::FACTORY_PASSWORD;
        $userData['password_confirmation'] = $isInvalidPassword ? $invalidPassword : User::FACTORY_PASSWORD;

        return $userData;
    }


    public function testSuccessfulRegistration(): void
    {
        $userData = $this->getUserDataToRegister();

        $response = $this
            ->postJson('/api/register', $userData)
            ->assertSuccessful()
            ->assertJson([
                'user' => true,
                'auth_token' => true,
            ])
            ->assertJsonPath('user.email', $userData['email']);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ])->assertDatabaseHas('subscriptions', [
            'user_id' => $response['user']['id'],
        ]);
    }


    public function testRegistrationWhenEmailTaken(): void
    {
        $email = 'example@example.com';

        User::factory()->create([ 'email' => $email ]);

        $userData = $this->getUserDataToRegister();
        $userData['email'] = $email;

        $this
            ->postJson('/api/register', $userData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }


    public function testRegistrationWithInvalidData(): void
    {
        $userData = $this->getUserDataToRegister(true);

        $this
            ->postJson('/api/register', $userData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', [
            'email' => $userData['email'],
        ]);
    }
}
