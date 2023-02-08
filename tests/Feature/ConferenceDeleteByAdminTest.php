<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Conference;


class ConferenceDeleteByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected function testNotAdminTryingToDeleteConference(string $userType): void
    {
        $conference = Conference::factory()->create();

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->deleteJson('/nova-api/conferences?resources[]='.$conference->id)
            ->assertForbidden();

        $this->assertModelExists($conference);
    }


    public function testSuccessfulDeleteConference(): void
    {
        $conference = Conference::factory()->create();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/conferences?resources[]='.$conference->id)
            ->assertSuccessful();

        $this->assertModelMissing($conference);
    }


    public function testListenerTryingToDeleteConference(): void
    {
        $this->testNotAdminTryingToDeleteConference('listener');
    }


    public function testAnnouncerTryingToDeleteConference(): void
    {
        $this->testNotAdminTryingToDeleteConference('announcer');
    }


    public function testUnauthorizedTryingToDeleteConference(): void
    {
        $conference = Conference::factory()->create();

        $this
            ->deleteJson('/nova-api/conferences?resources[]='.$conference->id)
            ->assertUnauthorized();

        $this->assertModelExists($conference);
    }


    public function testDeleteWhenConferenceDoesNotExists(): void
    {
        $deletedConference = tap(Conference::factory()->create())->delete();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/conferences?resources[]='.$deletedConference->id)
            ->assertSuccessful();

        $this->assertModelMissing($deletedConference);
    }
}
