<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;


class CategoryStoreByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected function testNotAdminTryingToStoreCategory(string $userType): void
    {
        $category = Category::factory()->make();

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->postJson(
                '/nova-api/categories/',
                $category->toArray()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }


    public function testSuccessfulStoreCategory(): void
    {
        $response = $this
            ->actingAs(User::factory()->admin()->create())
            ->postJson(
                '/nova-api/categories/',
                Category::factory()->make()->toArray()
            )
            ->assertSuccessful();

        $this->assertDatabaseHas('categories', [
            'id' => $response['id'],
        ]);
    }


    public function testListenerTryingToStoreCategory(): void
    {
        $this->testNotAdminTryingToStoreCategory('listener');
    }


    public function testAnnouncerTryingToStoreCategory(): void
    {
        $this->testNotAdminTryingToStoreCategory('announcer');
    }


    public function testUnauthorizedTryingToStoreCategory(): void
    {
        $category = Category::factory()->make();

        $this
            ->postJson(
                '/nova-api/categories/',
                $category->toArray()
            )
            ->assertUnauthorized();

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }


    public function testStoreCategoryWithInvalidData(): void
    {
        $category = Category::factory()->make();
        $category->title = '';

        $this
            ->actingAs(User::factory()->admin()->create())
            ->postJson(
                '/nova-api/categories/',
                $category->toArray()
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }
}
