<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;


class UpdateUserProfileTest extends TestCase
{
    use RefreshDatabase;


    private function getUpdatedUserData(User $user, string $newFirstName): array
    {
        $arrayUser = $user->toArray();

        $arrayUser['password'] = User::FACTORY_PASSWORD;
        $arrayUser['password_confirmation'] = User::FACTORY_PASSWORD;

        $arrayUser['first_name'] = $newFirstName;

        return $arrayUser;
    }


    private function assertUserHasUpdated(User $user, string $oldFirstName, string $newFirstName): void
    {
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $newFirstName,
        ])
        ->assertDatabaseMissing('users', [
            'id' => $user->id,
            'first_name' => $oldFirstName,
        ]);
    }


    private function assertUserHasNotUpdated(User $user, string $oldFirstName, string $newFirstName): void
    {
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'first_name' => $newFirstName,
        ])
        ->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $oldFirstName,
        ]);
    }


    public function testSuccessfulUpdateUserProfile(): void
    {
        $firstName = 'Test';
        $newFirstName = 'Updated';

        $user = User::factory()->create(['first_name' => $firstName]);
        $updatedUser = $this->getUpdatedUserData($user, $newFirstName);

        $response = $this->actingAs($user)->postJson('/api/profile/update', $updatedUser);

        $response
            ->assertSuccessful()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('first_name', $newFirstName);

        $this->assertUserHasUpdated($user, $firstName, $newFirstName);
    }


    public function testUnauthorizedTryingToUpdateProfile(): void
    {
        $firstName = 'Test';
        $newFirstName = 'Updated';

        $user = User::factory()->create(['first_name' => $firstName]);
        $updatedUser = $this->getUpdatedUserData($user, $newFirstName);

        $this
            ->postJson('/api/profile/update', $updatedUser)
            ->assertUnauthorized();

        $this->assertUserHasNotUpdated($user, $firstName, $newFirstName);
    }


    public function testUpdateProfileWhenEmailTaken(): void
    {
        $firstName = 'Test';
        $newFirstName = 'Updated';

        $email = 'test@test.com';

        User::factory()->create(['email' => $email]);
        $user = User::factory()->create(['first_name' => $firstName]);

        $updatedUser = $this->getUpdatedUserData($user, $newFirstName);
        $updatedUser['email'] = $email;

        $response = $this->actingAs($user)->postJson('/api/profile/update', $updatedUser);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertUserHasNotUpdated($user, $firstName, $newFirstName);
    }


    public function testUpdateProfileWithInvalidData(): void
    {
        $firstName = 'Test';
        $newFirstName = '';

        $user = User::factory()->create(['first_name' => $firstName]);
        $updatedUser = $this->getUpdatedUserData($user, $newFirstName);

        $response = $this->actingAs($user)->postJson('/api/profile/update', $updatedUser);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name']);

        $this->assertUserHasNotUpdated($user, $firstName, $newFirstName);
    }
}
