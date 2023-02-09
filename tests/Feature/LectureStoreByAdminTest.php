<?php

declare(strict_types=1);

namespace Tests\Feature;

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


    // public function testSuccessfulStoreLecture(): void
    // {
    //     // dd($this->getLectureDataToStore());

    //     $response = $this
    //         ->actingAs(User::factory()->admin()->create())
    //         ->postJson(
    //             '/nova-api/lectures/',
    //             $this->getLectureDataToStore()
    //         )
    //         ->assertSuccessful();

    //     $this->assertDatabaseHas('lectures', [
    //         'id' => $response['id'],
    //     ]);
    // }
}
