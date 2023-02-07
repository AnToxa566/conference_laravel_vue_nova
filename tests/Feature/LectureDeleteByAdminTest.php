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


    public function testSuccessfulLectureDelete(): void
    {
        $admin = User::factory()->admin()->create();
        $lecture = Lecture::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/lectures?resources[]='.$lecture->id);

        $response->assertSuccessful();
        $this->assertModelMissing($lecture);
    }


    public function testListenerTryingToDeleteLecture(): void
    {
        $listener = User::factory()->listener()->create();
        $lecture = Lecture::factory()->create();

        $response = $this->actingAs($listener)->deleteJson('/nova-api/lectures?resources[]='.$lecture->id);

        $response->assertForbidden();
        $this->assertModelExists($lecture);
    }


    public function testAnnouncerTryingToDeleteLecture(): void
    {
        $announcer = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->create();

        $response = $this->actingAs($announcer)->deleteJson('/nova-api/lectures?resources[]='.$lecture->id);

        $response->assertForbidden();
        $this->assertModelExists($lecture);
    }


    public function testUnauthorizedTryingToDeleteLecture(): void
    {
        $lecture = Lecture::factory()->create();

        $response = $this->deleteJson('/nova-api/lectures?resources[]='.$lecture->id);

        $response->assertUnauthorized();
        $this->assertModelExists($lecture);
    }


    public function testDeleteWhenLectureDoesNotExists(): void
    {
        $admin = User::factory()->admin()->create();
        $deletedLecture = tap(Lecture::factory()->create())->delete();

        $response = $this->actingAs($admin)->deleteJson('/nova-api/lectures?resources[]='.$deletedLecture->id);

        $response->assertSuccessful();
        $this->assertModelMissing($deletedLecture);
    }
}
