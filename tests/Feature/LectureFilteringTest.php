<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;
use App\Models\Category;
use App\Models\Conference;


class LectureFilteringTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulLectureFilteringTest(): void
    {
        $lecture = Lecture::factory()->forCategory()->create();

        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/lectures/filtered', [
                'conferenceId' => $lecture->conference_id,
                'categoriesId' => [$lecture->category_id],
            ])
            ->assertSuccessful()
            ->assertJsonPath('0.title', $lecture->title)
            ->assertJsonPath('0.category_id', $lecture->category_id);
    }


    public function testUnauthorizedTryingToFilter(): void
    {
        $this
            ->postJson('/api/lectures/filtered', [
                'conferenceId' => (Lecture::factory()->create())->conference_id,
            ])
            ->assertUnauthorized();
    }


    public function testFilteringWithInvalidDuration(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/lectures/filtered', [
                'conferenceId' => (Lecture::factory()->create())->conference_id,

                'minDuration' => 1,
                'maxDuration' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minDuration', 'maxDuration']);
    }


    public function testFilteringWithInvalidTime(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/lectures/filtered', [
                'conferenceId' => (Lecture::factory()->create())->conference_id,

                'startTimeAfter' => now()->addHour()->format('H:i:s'),
                'startTimeBefore' => now()->format('H:i:s'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['startTimeAfter', 'startTimeBefore']);
    }


    public function testFilteringWithInvalidCategories(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/lectures/filtered', [
                'conferenceId' => (Lecture::factory()->create())->conference_id,
                'categoriesId' => ['invalid'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['categoriesId.0']);
    }
}
