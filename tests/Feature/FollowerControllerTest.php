<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
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
            ->json('post', 'api/users/' . $this->followed->getAttribute('uuid') . '/toggle-follow')
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('followers', $this->payload);
    }

    public function testFollowerEntryIsDestroyedSuccessfully()
    {
        $this->follower->followed()->toggle($this->followed);
        $this->actingAs($this->follower)
            ->json('post', 'api/users/' . $this->followed->getAttribute('uuid') . '/toggle-follow')
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('followers', $this->payload);
    }

    public function testFollowerCannotFollowHimself()
    {
        $this->actingAs($this->follower)
            ->json('post', 'api/users/' . $this->follower->getAttribute('uuid') . '/toggle-follow')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testMissingUuidReturnsNotFound()
    {
        $this->actingAs($this->follower)
            ->json('post', 'api/users/' . $this->nonExistingUuid . '/toggle-follow')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testMissingUuidReturnsError()
    {

        $this->actingAs($this->follower)
            ->json('post', 'api/users/' . $this->nonExistingUuid . '/toggle-follow')
            ->assertJsonStructure(['message']);
    }
}
