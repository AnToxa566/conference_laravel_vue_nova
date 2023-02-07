<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Comment;


class CommentUpdateTest extends TestCase
{
    use RefreshDatabase;


    private function assertCommentHasUpdated(Comment $comment, string $oldDescription, string $newDescription): void
    {
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'description' => $newDescription,
        ])
        ->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'description' => $oldDescription,
        ]);
    }


    private function assertCommentHasNotUpdated(Comment $comment, string $oldDescription, string $newDescription): void
    {
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'description' => $newDescription,
        ])
        ->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'description' => $oldDescription,
        ]);
    }


    public function testSuccessfulCommentUpdate(): void
    {
        $oldDescription = 'Old description';
        $newDescription = 'New description';

        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create([
            'description' => $oldDescription,
        ]);

        $response = $this->actingAs($user)->postJson('/api/comments/'.$comment->id.'/update', [
            'description' => $newDescription,
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonPath('user_id', $comment->user_id)
            ->assertJsonPath('lecture_id', $comment->lecture_id)
            ->assertJsonPath('description', $newDescription);

        $this->assertCommentHasUpdated($comment, $oldDescription, $newDescription);
    }


    public function testUnauthorizedTryingToUpdateComment(): void
    {
        $oldDescription = 'Old description';
        $newDescription = 'New description';

        $comment = Comment::factory()->create([
            'description' => $oldDescription,
        ]);

        $response = $this->postJson('/api/comments/'.$comment->id.'/update', [
            'description' => $newDescription,
        ]);

        $response->assertUnauthorized();

        $this->assertCommentHasNotUpdated($comment, $oldDescription, $newDescription);
    }


    public function testNotCommentOwnerTryingToUpdateComment(): void
    {
        $oldDescription = 'Old description';
        $newDescription = 'New description';

        $comment = Comment::factory()->create([
            'description' => $oldDescription,
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)->postJson('/api/comments/'.$comment->id.'/update', [
            'description' => $newDescription,
        ]);

        $response->assertForbidden();

        $this->assertCommentHasNotUpdated($comment, $oldDescription, $newDescription);
    }


    public function testUpdateCommentWithInvalidDescription(): void
    {
        $oldDescription = 'Old description';
        $invalidDescription = '';

        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create([
            'description' => $oldDescription,
        ]);

        $response = $this->actingAs($user)->postJson('/api/comments/'.$comment->id.'/update', [
            'description' => $invalidDescription,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['description']);

        $this->assertCommentHasNotUpdated($comment, $oldDescription, $invalidDescription);
    }


    public function testUpdateCommentWhenCommentDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/comments/0/update', [])
            ->assertNotFound();
    }
}
