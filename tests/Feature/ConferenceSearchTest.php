<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Conference;


class ConferenceSearchTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulConferenceSearch(): void
    {
        $conference = Conference::factory()->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/conferences/search/'.$conference->title.'/limit/1');

        $response
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $conference->id)
            ->assertJsonPath('0.title', $conference->title);
    }


    public function testUnauthorizedUserTryingToSearchConference(): void
    {
        $this
            ->getJson('/api/conferences/search/'.(Conference::factory()->create())->title.'/limit/1')
            ->assertUnauthorized();
    }


    public function testSearchConferenceWhichDoesNotExists(): void
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/conferences/search/missing/limit/1');

        $response
            ->assertSuccessful()
            ->assertJsonCount(0)
            ->assertJsonMissingPath('0.title');
    }


    public function testSearchConferenceWithInvalidLimit(): void
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/conferences/search/'.(Conference::factory()->create())->title.'/limit/0');

        $response
            ->assertSuccessful()
            ->assertJsonCount(0)
            ->assertJsonMissingPath('0.title');
    }
}
