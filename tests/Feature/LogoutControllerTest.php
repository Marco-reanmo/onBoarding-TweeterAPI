<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserIsLoggedOutSuccessfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->withHeaders([
                'referer' => config('sanctum.stateful'),
            ])
            ->postJson('api/logout')
            ->assertNoContent();
    }

    public function testUnauthenticatedUserCannotLogout()
    {
        $this->postJson('api/logout')
            ->assertUnauthorized();
    }
}
