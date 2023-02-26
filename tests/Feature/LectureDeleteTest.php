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


    public function testSuccessfulLectureDelete(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->for($user)->create();

        $this
            ->actingAs($user)
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertSuccessful()
            ->assertJsonPath('id', $lecture->id)
            ->assertJsonPath('title', $lecture->title);

        $this->assertModelMissing($lecture);
    }


    public function testUnauthorizedTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertUnauthorized();

        $this->assertModelExists($lecture);
    }


    public function testListenerTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs(User::factory()->listener()->create())
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertForbidden();

        $this->assertModelExists($lecture);
    }


    public function testNotLectureOwnerTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs(User::factory()->announcer()->create())
            ->getJson('/api/lectures/'.$lecture->id.'/delete')
            ->assertForbidden();

        $this->assertModelExists($lecture);
    }


    public function testDeleteWhenLectureDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->announcer()->create())
            ->getJson('/api/lectures/0/delete')
            ->assertNotFound();
    }
}
