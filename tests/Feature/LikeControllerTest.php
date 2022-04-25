<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    private User $currentUser;
    private Tweet $currentTweet;
    private User $otherUser;
    private Tweet $otherTweet;
    private string $missingUuid;

    public function setUp(): void
    {
        parent::setUp();
        $tweets = Tweet::factory(2)->create();
        $this->currentTweet = $tweets->first();
        $this->otherTweet = $tweets->last();
        $this->currentUser = $this->currentTweet->author;
        $this->otherUser = $this->otherTweet->author;
        $this->missingUuid = '0';
    }

    public function testGetLikesReturnsInstanceOfUserCollection()
    {
        $response = $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes');
        $response->assertOk();
        $this->assertInstanceOf(Collection::class, $response->getOriginalContent());
        $response->assertJsonStructure(['users']);
    }

    public function testUserCanGetUsersWhoLikedOwnTweet()
    {
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
    }

    public function testUserCanGetUsersWhoLikedTweetFromOtherUser()
    {
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->otherTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
    }

    public function testShowWithoutLikesReturnsValidNumberOfUsers()
    {
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertOk()
            ->assertJsonCount(0, 'users');
    }

    public function testShowOneLikeReturnsValidNumberOfUsers()
    {
        $this->currentTweet->usersWhoLiked()->attach($this->otherUser);
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertOk()
            ->assertJsonCount(1, 'users');
    }

    public function testShowMoreThanOneLikeReturnsValidNumberOfUsers()
    {
        $randomNumberOfUsers = rand(2,20);
        $tweet = Tweet::factory()->has(User::factory($randomNumberOfUsers), 'usersWhoLiked')->create();
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $tweet->getAttribute('uuid') . '/likes')
            ->assertOk()
            ->assertJsonCount($randomNumberOfUsers, 'users');
    }

    public function testShowMissingTweetReturnsNotFound()
    {
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->missingUuid . '/likes')
            ->assertNotFound();
    }

    public function testShowMissingTweetReturnsError()
    {
        $this->actingAs($this->currentUser)
            ->getJson('api/tweets/' . $this->missingUuid . '/likes')
            ->assertJsonStructure(['message']);
    }

    public function testUserCanLikeOwnTweet()
    {
        $this->actingAs($this->currentUser)
            ->postJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->currentUser->id,
            'tweet_id' => $this->currentTweet->id
        ]);
    }

    public function testUserCanUnlikeOwnTweet()
    {
        $this->currentTweet->usersWhoLiked()->attach($this->currentUser);
        $this->actingAs($this->currentUser)
            ->postJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->currentUser->id,
            'tweet_id' => $this->currentTweet->id
        ]);
    }

    public function testUserCanLikeTweetFromOtherUser()
    {
        $this->actingAs($this->currentUser)
            ->postJson('api/tweets/' . $this->otherTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->currentUser->id,
            'tweet_id' => $this->otherTweet->id
        ]);
    }

    public function testUserCanUnlikeTweetFromOtherUser()
    {
        $this->otherTweet->usersWhoLiked()->attach($this->currentUser);
        $this->actingAs($this->currentUser)
            ->postJson('api/tweets/' . $this->otherTweet->getAttribute('uuid') . '/likes')
            ->assertOk();
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->currentUser->id,
            'tweet_id' => $this->otherTweet->id
        ]);
    }

    public function testUserCannotLikeMissingTweet()
    {
        $this->actingAs($this->currentUser)
            ->postJson('api/tweets/' . $this->missingUuid . '/likes')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function testUnauthenticatedUserCannotGetUsersWhoLikedTweet()
    {
        $this->getJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertUnauthorized();
    }

    public function testUnauthenticatedUserCannotLikeTweet()
    {
        $this->postJson('api/tweets/' . $this->currentTweet->getAttribute('uuid') . '/likes')
            ->assertUnauthorized();
    }
}
