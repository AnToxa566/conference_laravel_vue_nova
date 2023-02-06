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


    public function testSuccessfulFiltering(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $conference = Conference::factory()->create();

        Lecture::factory()->for($user)->for($conference)->for($category)->create();

        $response = $this->actingAs($user)->postJson('/api/lectures/filtered', [
            'conferenceId' => $conference->id,
            'categoriesId' => [$category->id],
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonPath('0.category_id', $category->id);
    }


    public function testUnauthorizedFiltering(): void
    {
        $conference = Conference::factory()->create();

        $this->postJson('/api/lectures/filtered', [
            'conferenceId' => $conference->id,
        ])->assertUnauthorized();
    }


    public function testInvalidDurationFiltering(): void
    {
        $user = User::factory()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/lectures/filtered', [
            'conferenceId' => $conference->id,

            'minDuration' => 1,
            'maxDuration' => 0,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minDuration', 'maxDuration']);
    }


    public function testInvalidTimeFiltering(): void
    {
        $user = User::factory()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/lectures/filtered', [
            'conferenceId' => $conference->id,

            'startTimeAfter' => now()->addHour()->format('H:i:s'),
            'startTimeBefore' => now()->format('H:i:s'),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['startTimeAfter', 'startTimeBefore']);
    }


    public function testInvalidCategoriesFiltering(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $conference = Conference::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/lectures/filtered', [
            'conferenceId' => $conference->id,
            'categoriesId' => [$category->id, 'invalid'],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['categoriesId.1']);
    }
}
