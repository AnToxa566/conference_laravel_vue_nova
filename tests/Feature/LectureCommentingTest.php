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


    public function testSuccessfulLectureCommenting(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->make();

        $response = $this->actingAs($user)->postJson('/api/comments/add', [
            'user_id'       => $comment->user_id,
            'lecture_id'    => $comment->lecture_id,

            'description'   => $comment->description,
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonPath('user_id', $comment->user_id)
            ->assertJsonPath('lecture_id', $comment->lecture_id)
            ->assertJsonPath('description', $comment->description);

        $this->assertDatabaseHas('comments', [
            'id' => $response['id'],
        ]);
    }


    public function testUnauthorizedUserTryingToCommentLecture(): void
    {
        $comment = Comment::factory()->make();

        $response = $this->postJson('/api/comments/add', [
            'user_id'       => $comment->user_id,
            'lecture_id'    => $comment->lecture_id,

            'description'   => $comment->description,
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('comments', [
            'lecture_id' => $comment->lecture_id,
        ]);
    }


    public function testLectureCommentingWhenUserDoesNotExists(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->make();

        $response = $this->actingAs($user)->postJson('/api/comments/add', [
            'user_id'       => 0,
            'lecture_id'    => $comment->lecture_id,

            'description'   => $comment->description,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);

        $this->assertDatabaseMissing('comments', [
            'lecture_id' => $comment->lecture_id,
        ]);
    }


    public function testLectureCommentingWhenLectureDoesNotExists(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->make();

        $response = $this->actingAs($user)->postJson('/api/comments/add', [
            'user_id'       => $comment->user_id,
            'lecture_id'    => 0,

            'description'   => $comment->description,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lecture_id']);

        $this->assertDatabaseMissing('comments', [
            'lecture_id' => $comment->lecture_id,
        ]);
    }


    public function testLectureCommentingWithInvalidDescription(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->make();

        $response = $this->actingAs($user)->postJson('/api/comments/add', [
            'user_id'       => $comment->user_id,
            'lecture_id'    => $comment->lecture_id,

            'description'   => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['description']);

        $this->assertDatabaseMissing('comments', [
            'lecture_id' => $comment->lecture_id,
        ]);
    }
}
