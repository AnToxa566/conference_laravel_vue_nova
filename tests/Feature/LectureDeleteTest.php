<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureDeleteTest extends TestCase
{
    use RefreshDatabase;


    private function assertLectureHasNotDeleted(int $lectureId): void
    {
        $this->assertDatabaseHas('lectures', [
            'id' => $lectureId,
        ]);
    }


    public function testSuccessfulLectureDelete(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/lectures/'.$lecture->id.'/delete');

        $response
            ->assertSuccessful()
            ->assertJsonPath('id', $lecture->id);

        $this->assertDatabaseMissing('lectures', [
            'id' => $lecture->id,
        ]);
    }


    public function testUnauthorizedUserTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertUnauthorized();

        $this->assertLectureHasNotDeleted($lecture->id);
    }


    public function testListenerTryingToUpdateLecture(): void
    {
        $user = User::factory()->listener()->create();
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs($user)
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertForbidden();

        $this->assertLectureHasNotDeleted($lecture->id);
    }


    public function testNotLectureOwnerTryingToDeleteLecture(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs($user)
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertForbidden();

        $this->assertLectureHasNotDeleted($lecture->id);
    }


    public function testDeleteWhenLectureDoesNotExists(): void
    {
        $user = User::factory()->announcer()->create();

        $this
            ->actingAs($user)
            ->getJson('/api/lectures/0/delete')
            ->assertNotFound();
    }
}
