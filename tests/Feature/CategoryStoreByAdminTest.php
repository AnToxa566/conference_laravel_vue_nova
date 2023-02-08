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


    public function testSuccessfulStoreCategory(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->postJson(
            '/nova-api/categories?editing=true&editMode=create',
            Category::factory()->make()->toArray()
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('categories', [
            'id' => $response['id'],
        ]);
    }


    public function testListenerTryingToStoreCategory(): void
    {
        $category = Category::factory()->make();

        $this
            ->actingAs(User::factory()->listener()->create())
            ->postJson(
                '/nova-api/categories?editing=true&editMode=create',
                $category->toArray()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }


    public function testAnnouncerTryingToStoreCategory(): void
    {
        $category = Category::factory()->make();

        $this
            ->actingAs(User::factory()->announcer()->create())
            ->postJson(
                '/nova-api/categories?editing=true&editMode=create',
                $category->toArray()
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }


    public function testUnauthorizedTryingToStoreCategory(): void
    {
        $category = Category::factory()->make();

        $this
            ->postJson(
                '/nova-api/categories?editing=true&editMode=create',
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

        $response = $this->actingAs(User::factory()->admin()->create())
        ->postJson(
            '/nova-api/categories?editing=true&editMode=create',
            $category->toArray()
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseMissing('categories', [
            'title' => $category->title,
        ]);
    }
}
