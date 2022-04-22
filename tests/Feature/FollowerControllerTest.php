<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FollowerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $follower;
    private User $followed;
    private array $payload;
    private string $nonExistingUuid;

    protected function setUp(): void
    {
        parent::setUp();
        $users = User::factory(2)->create();
        $this->follower = $users->first();
        $this->followed = $users->last();
        $this->payload = [
            'follower_id' => $this->follower->id,
            'followed_id' => $this->followed->id,
        ];
        $this->nonExistingUuid = '0';
    }

    public function testFollowerEntryIsCreatedSuccessfully()
    {
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->followed->getAttribute('uuid') . '/toggle-follow')
            ->assertOk();
        $this->assertDatabaseHas('followers', $this->payload);
    }

    public function testFollowerEntryIsDestroyedSuccessfully()
    {
        $this->follower->followed()->toggle($this->followed);
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->followed->getAttribute('uuid') . '/toggle-follow')
            ->assertOk();
        $this->assertDatabaseMissing('followers', $this->payload);
    }

    public function testFollowerCannotFollowHimself()
    {
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->follower->getAttribute('uuid') . '/toggle-follow')
            ->assertForbidden();
    }

    public function testMissingUuidReturnsNotFound()
    {
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->nonExistingUuid . '/toggle-follow')
            ->assertNotFound();
    }

    public function testMissingUuidReturnsError()
    {
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->nonExistingUuid . '/toggle-follow')
            ->assertJsonStructure(['message']);
    }

    public function testUnauthorizedUserCannotFollowUser()
    {
        $this->postJson('api/users/' . $this->follower->getAttribute('uuid') . '/toggle-follow')
            ->assertUnauthorized();
    }
}
