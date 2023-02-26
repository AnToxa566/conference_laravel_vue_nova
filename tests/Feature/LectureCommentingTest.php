<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Comment;


class LectureCommentingTest extends TestCase
{
    use RefreshDatabase;


    protected function assertDatabaseMissingComment(Comment $comment): void
    {
        $this->assertDatabaseMissing('comments', [
            'user_id' => $comment->user_id,
            'lecture_id' => $comment->lecture_id,
        ]);
    }


    protected function testLectureCommentingWithInvalidData(string $attribute, string $invalidData = null): void
    {
        $comment = Comment::factory()->make();
        $comment[$attribute] = $invalidData;

        $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/comments/add', $comment->toArray())
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$attribute]);

        $this->assertDatabaseMissingComment($comment);
    }


    public function testSuccessfulLectureCommenting(): void
    {
        $comment = Comment::factory()->make();

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/comments/add', $comment->toArray())
            ->assertSuccessful()
            ->assertJsonPath('user_id', $comment->user_id)
            ->assertJsonPath('lecture_id', $comment->lecture_id)
            ->assertJsonPath('description', $comment->description);

        $this->assertDatabaseHas('comments', [
            'id' => $response['id'],
        ]);
    }


    public function testUnauthorizedTryingToCommentLecture(): void
    {
        $comment = Comment::factory()->make();

        $this
            ->postJson('/api/comments/add', $comment->toArray())
            ->assertUnauthorized();

        $this->assertDatabaseMissingComment($comment);
    }


    public function testLectureCommentingWithInvalidUser(): void
    {
        $this->testLectureCommentingWithInvalidData('user_id');
    }


    public function testLectureCommentingWithInvalidLecture(): void
    {
        $this->testLectureCommentingWithInvalidData('lecture_id');
    }


    public function testLectureCommentingWithInvalidDescription(): void
    {
        $this->testLectureCommentingWithInvalidData('description');
    }
}
