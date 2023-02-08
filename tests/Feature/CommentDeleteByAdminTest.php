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


    protected function testNotAdminTryingToDeleteComment(string $userType): void
    {
        $comment = Comment::factory()->create();

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->deleteJson('/nova-api/comments?resources[]='.$comment->id)
            ->assertForbidden();

        $this->assertModelExists($comment);
    }


    public function testSuccessfulDeleteComment(): void
    {
        $comment = Comment::factory()->create();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/comments?resources[]='.$comment->id)
            ->assertSuccessful();

        $this->assertModelMissing($comment);
    }


    public function testListenerTryingToDeleteComment(): void
    {
        $this->testNotAdminTryingToDeleteComment('listener');
    }


    public function testAnnouncerTryingToDeleteComment(): void
    {
        $this->testNotAdminTryingToDeleteComment('announcer');
    }


    public function testUnauthorizedTryingToDeleteComment(): void
    {
        $comment = Comment::factory()->create();

        $this
            ->deleteJson('/nova-api/comments?resources[]='.$comment->id)
            ->assertUnauthorized();

        $this->assertModelExists($comment);
    }


    public function testDeleteWhenCommentDoesNotExists(): void
    {
        $deletedComment = tap(Comment::factory()->create())->delete();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/comments?resources[]='.$deletedComment->id)
            ->assertSuccessful();

        $this->assertModelMissing($deletedComment);
    }
}
