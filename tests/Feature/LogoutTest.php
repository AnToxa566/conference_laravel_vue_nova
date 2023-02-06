<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;


class LogoutTest extends TestCase
{
    use RefreshDatabase;


    public function testSuccessfulLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/logout');

        $response->assertSuccessful();
    }


    public function testUnauthorizedUserTryingToLogout(): void
    {
        $response = $this->getJson('/api/logout');

        $response->assertUnauthorized();
    }
}
