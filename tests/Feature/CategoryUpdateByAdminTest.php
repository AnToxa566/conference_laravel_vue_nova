<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Category;


class CategoryUpdateByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected const OLD_CATEGORY_TITLE = 'Old';

    protected const NEW_CATEGORY_TITLE = 'New';


    protected function getCategoryDataToUpdate(Category $category, string $newTitle = null): array
    {
        $arrayCtegory = $category->toArray();
        $arrayCtegory['title'] = $newTitle ?? self::NEW_CATEGORY_TITLE;

        return $arrayCtegory;
    }


    protected function assertCategoryHasUpdated(Category $category, string $oldTitle = null, string $newTitle = null): void
    {
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'title' => $newTitle ?? self::NEW_CATEGORY_TITLE,
        ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'title' => $oldTitle ?? self::OLD_CATEGORY_TITLE,
        ]);
    }


    protected function assertCategoryHasNotUpdated(Category $category, string $oldTitle = null, string $newTitle = null): void
    {
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'title' => $newTitle ?? self::NEW_CATEGORY_TITLE,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'title' => $oldTitle ?? self::OLD_CATEGORY_TITLE,
        ]);
    }


    protected function testNotAdminTryingToUpdateCategory(string $userType): void
    {
        $category = Category::factory()->create(['title' => self::OLD_CATEGORY_TITLE]);

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->putJson(
                '/nova-api/categories/'.$category->id,
                $this->getCategoryDataToUpdate($category)
            )
            ->assertForbidden();

        $this->assertCategoryHasNotUpdated($category);
    }


    public function testSuccessfulUpdateCategory(): void
    {
        $category = Category::factory()->create(['title' => self::OLD_CATEGORY_TITLE]);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson(
                '/nova-api/categories/'.$category->id,
                $this->getCategoryDataToUpdate($category)
            )
            ->assertSuccessful();

        $this->assertCategoryHasUpdated($category);
    }


    public function testListenerTryingToUpdateCategory(): void
    {
        $this->testNotAdminTryingToUpdateCategory('listener');
    }


    public function testAnnouncerTryingToUpdateCategory(): void
    {
        $this->testNotAdminTryingToUpdateCategory('announcer');
    }


    public function testUnauthorizedTryingToUpdateCategory(): void
    {
        $category = Category::factory()->create(['title' => self::OLD_CATEGORY_TITLE]);

        $this
            ->putJson(
                '/nova-api/categories/'.$category->id,
                $this->getCategoryDataToUpdate($category)
            )
            ->assertUnauthorized();

        $this->assertCategoryHasNotUpdated($category);
    }


    public function testUpdateCategoryWithInvalidData(): void
    {
        $category = Category::factory()->create(['title' => self::OLD_CATEGORY_TITLE]);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson(
                '/nova-api/categories/'.$category->id,
                $this->getCategoryDataToUpdate($category, '')
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertCategoryHasNotUpdated($category, self::OLD_CATEGORY_TITLE, '');
    }


    public function testUpdateCategoryWhenCategoryDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson('/nova-api/categories/0', [])
            ->assertNotFound();
    }
}
