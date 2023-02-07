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


    private function getLectureDataToUpdate(Lecture $lecture, string $newTitle): array
    {
        return [
            'user_id'           => $lecture->user->id,
            'conference_id'     => $lecture->conference->id,

            'title'             => $newTitle,
            'description'       => $lecture->description,

            'date_time_start'   => $lecture->date_time_start,
            'date_time_end'     => $lecture->date_time_end,
        ];
    }


    private function assertLectureUpdated(Lecture $oldLecture, string $newTitle): void
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


    private function assertLectureHasNotUpdated(Lecture $lecture, string $newTitle): void
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

        $response = $this->actingAs($user)->postJson(
            'api/lectures/'.$lecture->id.'/update',
            $this->getLectureDataToUpdate($lecture, $newTitle)
        );

        $response
            ->assertSuccessful()
            ->assertJsonPath('title', $newTitle);

        $this->assertLectureUpdated($lecture, $newTitle);
    }


    public function testUnauthorizedTryingToUpdateLecture(): void
    {
        $newTitle = 'New Title';
        $lecture = Lecture::factory()->create();

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
        $user = User::factory()->listener()->create();
        $lecture = Lecture::factory()->create();

        $newTitle = 'New Title';

        $this
            ->actingAs($user)
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertForbidden();

        $this->assertLectureHasNotUpdated($lecture, $newTitle);
    }


    public function testNotLectureOwnerTryingToUpdateLecture(): void
    {
        $user = User::factory()->announcer()->create();
        $lecture = Lecture::factory()->create();

        $newTitle = 'New Title';

        $this
            ->actingAs($user)
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

        $response = $this
            ->actingAs($user)
            ->postJson(
                'api/lectures/'.$lecture->id.'/update',
                $this->getLectureDataToUpdate($lecture, $newTitle)
            )
            ->assertUnprocessable();

        $response->assertJsonValidationErrors(['title']);

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
