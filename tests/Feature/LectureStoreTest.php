<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureStoreTest extends TestCase
{
    use RefreshDatabase;


    private function getDataToStoreLecture(): array
    {
        $lecture = Lecture::factory()->forUser()->forConference()->make();

        return [
            'user_id'           => $lecture->user->id,
            'conference_id'     => $lecture->conference->id,
            'category_id'       => $lecture->category ? $lecture->category->id : null,

            'title'             => $lecture->title,
            'description'       => $lecture->description,

            'date_time_start'   => $lecture->date_time_start,
            'date_time_end'     => $lecture->date_time_end,

            'presentation'      => UploadedFile::fake()->create('presentetion.pptx'),
            'is_online'         => $lecture->is_online,
        ];
    }


    private function assertLectureStored(array $lectureData, int $lectureId): void
    {
        $this
            ->assertDatabaseHas('lectures', [
                'id' => $lectureId,
            ])->assertDatabaseHas('conference_user', [
                'user_id' => $lectureData['user_id'],
                'conference_id' => $lectureData['conference_id'],
            ]);

        $lectureData['is_online']
            ? $this->assertDatabaseHas('zoom_meetings', [
                'lecture_id' => $lectureId,
            ])
            : $this->assertDatabaseMissing('zoom_meetings', [
                'lecture_id' => $lectureId,
            ]);
    }


    private function assertLectureHasNotStored(array $lectureData): void
    {
        $this
            ->assertDatabaseMissing('lectures', [
                'user_id' => $lectureData['user_id'],
                'conference_id' => $lectureData['conference_id'],
            ])->assertDatabaseMissing('conference_user', [
                'user_id' => $lectureData['user_id'],
                'conference_id' => $lectureData['conference_id'],
            ]);
    }


    public function testSuccessful(): void
    {
        $user = User::factory()->announcer()->create();
        $lectureData = $this->getDataToStoreLecture();

        $response = $this->actingAs($user)->postJson('/api/lectures/add', $lectureData);

        $response
            ->assertSuccessful()
            ->assertJson([
                'lecture' => true,
                'meeting' => $lectureData['is_online'],
            ])
            ->assertJsonPath('lecture.title', $lectureData['title']);

        $this->assertLectureStored($lectureData, $response['lecture']['id']);
    }


    public function testUnauthorized(): void
    {
        $lectureData = $this->getDataToStoreLecture();

        $this
            ->postJson('/api/lectures/add', $lectureData)
            ->assertUnauthorized();

        $this->assertLectureHasNotStored($lectureData);
    }


    public function testListener(): void
    {
        $user = User::factory()->listener()->create();
        $lectureData = $this->getDataToStoreLecture();

        $this
            ->actingAs($user)->postJson('/api/lectures/add', $lectureData)
            ->assertForbidden();

        $this->assertLectureHasNotStored($lectureData);
    }


    public function testInvalidData(): void
    {
        $user = User::factory()->announcer()->create();
        $lectureData = $this->getDataToStoreLecture();

        $lectureData['title'] = '';

        $this
            ->actingAs($user)->postJson('/api/lectures/add', $lectureData)
            ->assertUnprocessable();

        $this->assertLectureHasNotStored($lectureData);
    }
}
