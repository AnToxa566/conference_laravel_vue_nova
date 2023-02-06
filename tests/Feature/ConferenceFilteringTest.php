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
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Conference::factory()->for($category)->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/filtered', [
            'categoriesId' => [$category->id],
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonPath('0.category_id', $category->id);
    }


    public function testUnauthorizedUserTryingToFilter(): void
    {
        $this->postJson('/api/conferences/filtered')->assertUnauthorized();
    }


    public function testFilteringWithInvalidLectureCount(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/filtered', [
            'minLectureCount' => 1,
            'maxLectureCount' => 0,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minLectureCount', 'maxLectureCount']);
    }


    public function testFilteringWithInvalidDate(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/filtered', [
            'dateAfter'  => now()->addDays(1)->format('Y-m-d'),
            'dateBefore' => now(),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['dateAfter', 'dateBefore']);
    }


    public function testFilteringWithInvalidCategories(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conferences/filtered', [
            'categoriesId' => [$category->id, 'invalid'],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['categoriesId.1']);
    }
}
