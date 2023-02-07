<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Comment;


class CommentDeleteByAdminTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulDeleteComment(): void
    {
        $admin = User::factory()->admin()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/comments?resources[]='.$comment->id);

        $response->assertSuccessful();
        $this->assertModelMissing($comment);
    }


    public function testListenerTryingToDeleteComment(): void
    {
        $listener = User::factory()->listener()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($listener)->deleteJson('/nova-api/comments?resources[]='.$comment->id);

        $response->assertForbidden();
        $this->assertModelExists($comment);
    }


    public function testAnnouncerTryingToDeleteComment(): void
    {
        $announcer = User::factory()->announcer()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($announcer)->deleteJson('/nova-api/comments?resources[]='.$comment->id);

        $response->assertForbidden();
        $this->assertModelExists($comment);
    }


    public function testUnauthorizedTryingToDeleteComment(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson('/nova-api/comments?resources[]='.$comment->id);

        $response->assertUnauthorized();
        $this->assertModelExists($comment);
    }


    public function testDeleteWhenCommentDoesNotExists(): void
    {
        $admin = User::factory()->admin()->create();
        $deletedComment = tap(Comment::factory()->create())->delete();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/comments?resources[]='.$deletedComment->id);

        $response->assertSuccessful();
        $this->assertModelMissing($deletedComment);
    }
}
