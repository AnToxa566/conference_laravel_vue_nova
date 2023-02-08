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
        $user = User::factory()->create();
        $conference = Conference::factory()->create();

        $this
            ->actingAs($user)
            ->postJson('/api/conferences/join', [
                'conferenceId' => $conference->id,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('conference_user', [
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);
    }


    public function testUnauthorizedTryingToJoin(): void
    {
        $conference = Conference::factory()->create();

        $this
            ->postJson('/api/conferences/join', [
                'conferenceId' => $conference->id,
            ])
            ->assertUnauthorized();

        $this->assertDatabaseMissing('conference_user', [
            'conference_id' => $conference->id,
        ]);
    }


    public function testJoinWhenJoinsEnded(): void
    {
        $user = User::factory()->listener()->create();
        $conference = Conference::factory()->create();

        $user->joins_left = 0;
        $user->save();

        $this
            ->actingAs($user)
            ->postJson('/api/conferences/join', [
                'conferenceId' => $conference->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('conference_user', [
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);
    }


    public function testJoinWithInvalidConference(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/conferences/join', [
                'conferenceId' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('conferenceId');
    }
}
