<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureSearchTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulLectureSearch(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/lectures/search/'.$lecture->title.'/limit/1')
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $lecture->id)
            ->assertJsonPath('0.title', $lecture->title);
    }


    public function testUnauthorizedTryingToSearchLecture(): void
    {
        $this
            ->getJson('/api/lectures/search/test/limit/1')
            ->assertUnauthorized();
    }


    public function testSearchLectureWhichDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/lectures/search/missing/limit/1')
            ->assertSuccessful()
            ->assertJsonCount(0)
            ->assertJsonMissingPath('0.title');
    }


    public function testSearchLectureWithInvalidLimit(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/lectures/search/'.(Lecture::factory()->create())->title.'/limit/0')
            ->assertSuccessful()
            ->assertJsonCount(0)
            ->assertJsonMissingPath('0.title');
    }
}
