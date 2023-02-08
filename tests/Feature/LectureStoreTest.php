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
        $lecture = Lecture::factory()->make()->toArray();

        $lecture['presentation'] = UploadedFile::fake()->create('presentetion.pptx');

        return $lecture;
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


    public function testSuccessfulStoreLecture(): void
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
        $user = User::factory()->listener()->create();
        $lectureData = $this->getDataToStoreLecture();

        $this
            ->actingAs($user)->postJson('/api/lectures/add', $lectureData)
            ->assertForbidden();

        $this->assertLectureHasNotStored($lectureData);
    }


    public function testStoreLectureWithInvalidData(): void
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
