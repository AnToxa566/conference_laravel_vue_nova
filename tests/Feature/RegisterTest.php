<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Country;


class RegisterTest extends TestCase
{
    use RefreshDatabase;


    private function postRegister(string $email, bool $invalidPassword = false): TestResponse
    {
        return $this->postJson('/api/register', [
            'first_name'    => 'Example',
            'last_name'     => 'Example',

            'email'         => $email,

            'password'              => $invalidPassword ? '1234567' : '12345678',
            'password_confirmation' => $invalidPassword ? '1234567' : '12345678',

            'birthdate'     => '1999-01-01',
            'country'       => Country::TEST_COUNTRY_CODE,
            'type'          => User::LISTENER,

            'phone_number'          => '+380999999999',
            'country_phone_code'    => Country::TEST_COUNTRY_CODE,
        ]);
    }


    public function testSuccessfulRegistration(): void
    {
        $email = 'example@example.com';

        $response = $this->postRegister($email);

        $response
            ->assertSuccessful()
            ->assertJson([
                'auth_token' => true,
            ])
            ->assertJsonPath('user.email', $email);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ])->assertDatabaseHas('subscriptions', [
            'user_id' => $response['user']['id'],
        ]);
    }


    public function testEmailTaken(): void
    {
        $email = 'example@example.com';

        User::factory()->create([
            'email' => $email,
        ]);

        $response = $this->postRegister($email);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }


    public function testInvalidData(): void
    {
        $email = 'example@example.com';

        $response = $this->postRegister($email, true);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }
}
