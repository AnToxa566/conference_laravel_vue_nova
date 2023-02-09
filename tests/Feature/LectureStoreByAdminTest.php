<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lecture;


class LectureStoreByAdminTest extends TestCase
{
    use RefreshDatabase;


    protected function getLectureDataToStore(): array
    {
        $lectureData = Lecture::factory()->make()->toArray();

        $lectureData['conference'] = $lectureData['conference_id'];
        $lectureData['user'] = $lectureData['user_id'];

        return $lectureData;
    }


    protected function testNotAdminTryingToStoreLecture(string $userType): void
    {
        $lectureData = $this->getLectureDataToStore();

        $this
            ->actingAs(User::factory()->{$userType}()->create())
            ->postJson(
                '/nova-api/lectures/',
                $lectureData
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('lectures', [
            'title' => $lectureData['title'],
        ]);
    }


    public function testSuccessfulStoreLecture(): void
    {
        $response = $this
            ->actingAs(User::factory()->admin()->create())
            ->postJson(
                '/nova-api/lectures/',
                $this->getLectureDataToStore()
            )
            ->assertSuccessful();

        $this->assertDatabaseHas('lectures', [
            'id' => $response['id'],
        ]);
    }

    public function testListenerTryingToStoreLecture(): void
    {
        $this->testNotAdminTryingToStoreLecture('listener');
    }


    public function testAnnouncerTryingToStoreLecture(): void
    {
        $this->testNotAdminTryingToStoreLecture('announcer');
    }


    public function testUnauthorizedTryingToStoreLecture(): void
    {
        $lectureData = $this->getLectureDataToStore();

        $this
            ->postJson(
                '/nova-api/lectures/',
                $lectureData
            )
            ->assertUnauthorized();

        $this->assertDatabaseMissing('lectures', [
            'title' => $lectureData['title'],
        ]);
    }


    public function testStoreLectureWithInvalidData(): void
    {
        $lectureData = $this->getLectureDataToStore();
        $lectureData['title'] = '';

        $this
            ->actingAs(User::factory()->admin()->create())
            ->postJson(
                '/nova-api/lectures/',
                $lectureData
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseMissing('lectures', [
            'title' =>  $lectureData['title'],
        ]);
    }
}
