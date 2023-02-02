<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Conference;


class ListenerJoinConferenceTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulJoin(): void
    {
        $user = User::factory()->listener()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/join', [
            'conferenceId' => $conference->id,
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('conference_user', [
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);
    }


    public function testUnauthorizedUser(): void
    {
        $conference = Conference::factory()->create();

        $response = $this->postJson('/api/conferences/join', [
            'conferenceId' => $conference->id,
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('conference_user', [
            'conference_id' => $conference->id,
        ]);
    }


    public function testJoinsEnded(): void
    {
        $user = User::factory()->listener()->create();
        $conference = Conference::factory()->create();

        $user->joins_left = 0;
        $user->save();

        $response = $this->actingAs($user)->postJson('/api/conferences/join', [
            'conferenceId' => $conference->id,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('conference_user', [
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);
    }


    public function testConferenceDoesNotExists(): void
    {
        $user = User::factory()->listener()->create();
        $conference = tap(Conference::first())->delete();

        $response = $this->actingAs($user)->postJson('/api/conferences/join', [
            'conferenceId' => $conference->id,
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseMissing('conference_user', [
            'conference_id' => $conference->id,
        ]);
    }


    public function testInvalidData(): void
    {
        $user = User::factory()->listener()->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/join', [
            'conferenceId' => '',
        ]);

        $response->assertUnprocessable();
    }
}
