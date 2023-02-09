<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureUpdateByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected const OLD_LECTURE_TITLE = 'Old';

    protected const NEW_LECTURE_TITLE = 'New';


    protected function getLectureDataToUpdate(Lecture $lecture, string $newTitle = null): array
    {
        $lectureData = $lecture->toArray();

        $lectureData['title'] = $newTitle ?? self::NEW_LECTURE_TITLE;
        $lectureData['conference'] = $lectureData['conference_id'];
        $lectureData['user'] = $lectureData['user_id'];

        return $lectureData;
    }


    protected function assertLectureHasUpdated(Lecture $lecture, string $oldTitle = null, string $newTitle = null): void
    {
        $this->assertDatabaseHas('lectures', [
            'id' => $lecture->id,
            'title' => $newTitle ?? self::NEW_LECTURE_TITLE,
        ]);

        $this->assertDatabaseMissing('lectures', [
            'id' => $lecture->id,
            'title' => $oldTitle ?? self::OLD_LECTURE_TITLE,
        ]);
    }


    protected function assertLectureHasNotUpdated(Lecture $lecture, string $oldTitle = null, string $newTitle = null): void
    {
        $this->assertDatabaseHas('lectures', [
            'id' => $lecture->id,
            'title' => $oldTitle ?? self::OLD_LECTURE_TITLE,
        ]);

        $this->assertDatabaseMissing('lectures', [
            'id' => $lecture->id,
            'title' => $newTitle ?? self::NEW_LECTURE_TITLE,
        ]);
    }


    protected function testNotAdminTryingToUpdateLecture(string $userType): void
    {
        $lecture = Lecture::factory()->create(['title' => self::OLD_LECTURE_TITLE]);

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->putJson(
                '/nova-api/lectures/'.$lecture->id,
                $this->getLectureDataToUpdate($lecture)
            )
            ->assertForbidden();

        $this->assertLectureHasNotUpdated($lecture);
    }


    public function testSuccessfulUpdateLecture(): void
    {
        $lecture = Lecture::factory()->create(['title' => self::OLD_LECTURE_TITLE]);

        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson(
                '/nova-api/lectures/'.$lecture->id,
                $this->getLectureDataToUpdate($lecture)
            )
            ->assertSuccessful();

        $this->assertLectureHasUpdated($lecture);
    }


    public function testListenerTryingToUpdateLecture(): void
    {
        $this->testNotAdminTryingToUpdateLecture('listener');
    }


    public function testAnnouncerTryingToUpdateLecture(): void
    {
        $this->testNotAdminTryingToUpdateLecture('announcer');
    }


    public function testUnauthorizedTryingToUpdateLecture(): void
    {
        $lecture = Lecture::factory()->create(['title' => self::OLD_LECTURE_TITLE]);

        $this
            ->putJson(
                '/nova-api/lectures/'.$lecture->id,
                $this->getLectureDataToUpdate($lecture)
            )
            ->assertUnauthorized();

        $this->assertLectureHasNotUpdated($lecture);
    }


    public function testUpdateLectureWithInvalidData(): void
    {
        $lecture = Lecture::factory()->create(['title' => self::OLD_LECTURE_TITLE]);

        $invalidTitle = '';

        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson(
                '/nova-api/lectures/'.$lecture->id,
                $this->getLectureDataToUpdate($lecture, $invalidTitle)
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertLectureHasNotUpdated($lecture, self::OLD_LECTURE_TITLE, $invalidTitle);
    }


    public function testUpdateLectureWhenLectureDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->admin()->create())
            ->putJson('/nova-api/lectures/0')
            ->assertNotFound();
    }
}
