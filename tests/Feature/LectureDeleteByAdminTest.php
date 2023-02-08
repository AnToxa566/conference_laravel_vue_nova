<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureDeleteByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected function testNotAdminTryingToDeleteLecture(string $userType): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->deleteJson('/nova-api/lectures?resources[]='.$lecture->id)
            ->assertForbidden();

        $this->assertModelExists($lecture);
    }


    public function testSuccessfulLectureDelete(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/lectures?resources[]='.$lecture->id)
            ->assertSuccessful();

        $this->assertModelMissing($lecture);
    }


    public function testListenerTryingToDeleteLecture(): void
    {
        $this->testNotAdminTryingToDeleteLecture('listener');
    }


    public function testAnnouncerTryingToDeleteLecture(): void
    {
        $this->testNotAdminTryingToDeleteLecture('announcer');
    }


    public function testUnauthorizedTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $this
            ->deleteJson('/nova-api/lectures?resources[]='.$lecture->id)
            ->assertUnauthorized();

        $this->assertModelExists($lecture);
    }


    public function testDeleteWhenLectureDoesNotExists(): void
    {
        $deletedLecture = tap(Lecture::factory()->create())->delete();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->deleteJson('/nova-api/lectures?resources[]='.$deletedLecture->id)
            ->assertSuccessful();

        $this->assertModelMissing($deletedLecture);
    }
}
