<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;
use App\Models\Conference;


class ConferenceFilteringTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulConferenceFiltering(): void
    {
        $conference = Conference::factory()->forCategory()->create();

        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/conferences/filtered', [
                'categoriesId' => [$conference->category_id],
            ])
            ->assertSuccessful()
            ->assertJsonPath('0.category_id', $conference->category_id);
    }


    public function testUnauthorizedTryingToFilter(): void
    {
        $this->postJson('/api/conferences/filtered')->assertUnauthorized();
    }


    public function testFilteringWithInvalidLectureCount(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/conferences/filtered', [
                'minLectureCount' => 1,
                'maxLectureCount' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minLectureCount', 'maxLectureCount']);
    }


    public function testFilteringWithInvalidDate(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/conferences/filtered', [
                'dateAfter'  => now()->addDays(1)->format('Y-m-d'),
                'dateBefore' => now(),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['dateAfter', 'dateBefore']);
    }


    public function testFilteringWithInvalidCategories(): void
    {
        $this
            ->actingAs(User::factory()->create()
            )->postJson('/api/conferences/filtered', [
                'categoriesId' => ['invalid'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['categoriesId.0']);
    }
}
