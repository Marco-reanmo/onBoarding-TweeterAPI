<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FollowerControllerTest extends TestCase
{
    private User $follower;
    private User $followed;
    private array $payload;

    public function setUp(): void
    {
        parent::setUp();
        $users = User::factory(2)->create();
        $this->follower = $users->first();
        $this->followed = $users->last();
        $this->payload = [
            'follower_id' => $this->follower->id,
            'followed_id' => $this->followed->id,
        ];
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
            ->postJson('api/users/' . $this->fakeMissingUuid() . '/toggle-follow')
            ->assertNotFound();
    }

    private function fakeMissingUuid(): string
    {
        $uuid = $this->faker->uuid();
        return $this->isMissingUuid($uuid) ? $uuid : $this->fakeMissingUuid();
    }

    private function isMissingUuid(string $uuid): bool
    {
        return !(($uuid == $this->follower->uuid) || ($uuid == $this->followed->uuid));
    }

    public function testMissingUuidReturnsError()
    {
        $this->actingAs($this->follower)
            ->postJson('api/users/' . $this->fakeMissingUuid() . '/toggle-follow')
            ->assertJsonStructure(['message']);
    }

    public function testUnauthorizedUserCannotFollowUser()
    {
        $this->postJson('api/users/' . $this->follower->getAttribute('uuid') . '/toggle-follow')
            ->assertUnauthorized();
    }
}
