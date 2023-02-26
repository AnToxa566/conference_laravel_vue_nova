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


    protected const OLD_FIRST_NAME = 'Old';

    protected const NEW_FIRST_NAME = 'New';


    protected function getUpdatedUserData(User $user, string $newFirstName): array
    {
        $arrayUser = $user->toArray();

        $arrayUser['password'] = User::FACTORY_PASSWORD;
        $arrayUser['password_confirmation'] = User::FACTORY_PASSWORD;

        $arrayUser['first_name'] = $newFirstName;

        return $arrayUser;
    }


    protected function assertUserHasUpdated(User $user, string $oldFirstName, string $newFirstName): void
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


    protected function assertUserHasNotUpdated(User $user, string $oldFirstName, $newFirstName): void
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
        $user = User::factory()->create(['first_name' => self::OLD_FIRST_NAME]);

        $this
            ->actingAs($user)
            ->postJson('/api/profile/update', $this->getUpdatedUserData($user, self::NEW_FIRST_NAME))
            ->assertSuccessful()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('first_name', self::NEW_FIRST_NAME);

        $this->assertUserHasUpdated($user, self::OLD_FIRST_NAME, self::NEW_FIRST_NAME);
    }


    public function testUnauthorizedTryingToUpdateProfile(): void
    {
        $user = User::factory()->create(['first_name' => self::OLD_FIRST_NAME]);

        $this
            ->postJson('/api/profile/update', $this->getUpdatedUserData($user, self::NEW_FIRST_NAME))
            ->assertUnauthorized();

        $this->assertUserHasNotUpdated($user, self::OLD_FIRST_NAME, self::NEW_FIRST_NAME);
    }


    public function testUpdateProfileWhenEmailTaken(): void
    {
        $email = 'test@test.com';
        User::factory()->create(['email' => $email]);

        $user = User::factory()->create(['first_name' => self::OLD_FIRST_NAME]);

        $updatedUserData = $this->getUpdatedUserData($user, self::NEW_FIRST_NAME);
        $updatedUserData['email'] = $email;

        $this
            ->actingAs($user)
            ->postJson('/api/profile/update', $updatedUserData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertUserHasNotUpdated($user, self::OLD_FIRST_NAME, self::NEW_FIRST_NAME);
    }


    public function testUpdateProfileWithInvalidData(): void
    {
        $user = User::factory()->create(['first_name' => self::OLD_FIRST_NAME]);
        $updatedUser = $this->getUpdatedUserData($user, '');

        $this
            ->actingAs($user)
            ->postJson('/api/profile/update', $updatedUser)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name']);

        $this->assertUserHasNotUpdated($user, self::OLD_FIRST_NAME, $updatedUser['first_name']);
    }
}
