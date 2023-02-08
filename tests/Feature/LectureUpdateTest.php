<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureUpdateTest extends TestCase
{
    use RefreshDatabase;


    protected function getLectureDataToUpdate(Lecture $lecture, string $newTitle): array
    {
        $arrayLecture = $lecture->toArray();
        $arrayLecture['title'] = $newTitle;

        return $arrayLecture;
    }


    protected function assertLectureUpdated(Lecture $oldLecture, string $newTitle): void
    {
        $this
            ->assertDatabaseHas('lectures', [
                'id' => $oldLecture->id,
                'title' => $newTitle,
            ])
            ->assertDatabaseMissing('lectures', [
                'id' => $oldLecture->id,
                'title' => $oldLecture->title,
            ]);
    }


    protected function assertLectureHasNotUpdated(Lecture $lecture, string $newTitle): void
    {
        $this
            ->assertDatabaseMissing('lectures', [
                'id' => $lecture->id,
                'title' => $newTitle,
            ])
            ->assertDatabaseHas('lectures', [
                'id' => $lecture->id,
                'title' => $lecture->title,
            ]);
    }


    public function testSuccessfulLectureUpdate(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->for($user)->create();

        $newTitle = 'New Title';

        $this
            ->actingAs($user)
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertSuccessful()
            ->assertJsonPath('id', $lecture->id)
            ->assertJsonPath('title', $newTitle);

        $this->assertLectureUpdated($lecture, $newTitle);
    }


    public function testUnauthorizedTryingToUpdateLecture(): void
    {
        $lecture = Lecture::factory()->create();
        $newTitle = 'New Title';

        $this
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertUnauthorized();

        $this->assertLectureHasNotUpdated($lecture, $newTitle);
    }


    public function testListenerTryingToUpdateLecture(): void
    {
        $lecture = Lecture::factory()->create();
        $newTitle = 'New Title';

        $this
            ->actingAs(User::factory()->listener()->create())
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertForbidden();

        $this->assertLectureHasNotUpdated($lecture, $newTitle);
    }


    public function testNotLectureOwnerTryingToUpdateLecture(): void
    {
        $lecture = Lecture::factory()->create();
        $newTitle = 'New Title';

        $this
            ->actingAs(User::factory()->announcer()->create())
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertForbidden();

        $this->assertLectureHasNotUpdated($lecture, $newTitle);
    }


    public function testUpdateLectureWithInvalidData(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->for($user)->create();

        $newTitle = '';

        $this
            ->actingAs($user)
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertLectureHasNotUpdated($lecture, $newTitle);
    }


    public function testUpdateWhenLectureDoesNotExists(): void
    {
        $this
            ->actingAs(User::factory()->announcer()->create())
            ->postJson('api/lectures/0/update',[])
            ->assertNotFound();
    }
}
