<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Conference;


class DeleteConferenceTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulDeleteConference(): void
    {
        $admin = User::where('type', '=', User::ADMIN)->firstOrFail();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/conferences?resources[]='.$conference->id);

        $response->assertSuccessful();
        $this->assertModelMissing($conference);
    }


    public function testListenerTryingToDeleteConference(): void
    {
        $listener = User::factory()->listener()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($listener)->deleteJson('/nova-api/conferences?resources[]='.$conference->id);

        $response->assertForbidden();
        $this->assertModelExists($conference);
    }


    public function testAnnouncerTryingToDeleteConference(): void
    {
        $announcer = User::factory()->announcer()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($announcer)->deleteJson('/nova-api/conferences?resources[]='.$conference->id);

        $response->assertForbidden();
        $this->assertModelExists($conference);
    }


    public function testUnauthorizedTryingToDeleteConference(): void
    {
        $conference = Conference::factory()->create();

        $response = $this->deleteJson('/nova-api/conferences?resources[]='.$conference->id);

        $response->assertUnauthorized();
        $this->assertModelExists($conference);
    }


    public function testDeleteWhenConferenceDoesNotExists(): void
    {
        $admin = User::where('type', '=', User::ADMIN)->firstOrFail();
        $deletedConference = tap(Conference::firstOrFail())->delete();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/conferences?resources[]='.$deletedConference->id);

        $response->assertSuccessful();
        $this->assertModelMissing($deletedConference);
    }
}
