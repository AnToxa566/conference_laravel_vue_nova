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
        $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/logout')
            ->assertSuccessful();
    }


    public function testUnauthorizedTryingToLogout(): void
    {
        $this->getJson('/api/logout')->assertUnauthorized();
    }
}
