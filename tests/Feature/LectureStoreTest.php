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


    protected function getDataToStoreLecture(): array
    {
        $lecture = Lecture::factory()->make()->toArray();

        $lecture['presentation'] = UploadedFile::fake()->create('presentetion.pptx');

        return $lecture;
    }


    protected function assertLectureStored(array $lectureData, int $lectureId): void
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


    protected function assertLectureHasNotStored(array $lectureData): void
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


    public function testSuccessfulStoreLecture(): void
    {
        $lectureData = $this->getDataToStoreLecture();

        $response = $this
            ->actingAs(User::factory()->announcer()->create())
            ->postJson('/api/lectures/add', $lectureData)
            ->assertSuccessful()
            ->assertJson([
                'lecture' => true,
                'meeting' => $lectureData['is_online'],
            ])
            ->assertJsonPath('lecture.title', $lectureData['title']);

        $this->assertLectureStored($lectureData, $response['lecture']['id']);
    }


    public function testUnauthorizedTryingToStoreLecture(): void
    {
        $lectureData = $this->getDataToStoreLecture();

        $this
            ->postJson('/api/lectures/add', $lectureData)
            ->assertUnauthorized();

        $this->assertLectureHasNotStored($lectureData);
    }


    public function testListenerTryingToStoreLecture(): void
    {
        $lectureData = $this->getDataToStoreLecture();

        $this
            ->actingAs(User::factory()->listener()->create())
            ->postJson('/api/lectures/add', $lectureData)
            ->assertForbidden();

        $this->assertLectureHasNotStored($lectureData);
    }


    public function testStoreLectureWithInvalidData(): void
    {
        $lectureData = $this->getDataToStoreLecture();

        $lectureData['title'] = '';

        $this
            ->actingAs(User::factory()->announcer()->create())
            ->postJson('/api/lectures/add', $lectureData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertLectureHasNotStored($lectureData);
    }
}
