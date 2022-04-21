<?php

namespace App\Http\Resources;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tweet
 */
class TweetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'tweet' => [
                'author' => new UserResource($this->whenLoaded('author')),
                'image' => new ImageResource($this->whenLoaded('image')),
                'body' => $this->body,
                'comments_count' => $this->when(
                    isset($this->comments_count),
                    $this->comments_count
                ),
                'comments' => TweetResource::collection($this->whenLoaded('comments')),
                'human_created_at' => $this->created_at->diffForHumans(),
                'like_count' => $this->when(
                    isset($this->users_who_liked_count),
                    $this->users_who_liked_count
                )
            ],
            'links' => [
                'tweet' => 'api/tweets/' . $this->uuid,
                'toggle-like' => 'api/tweets/' . $this->uuid . '/like'
            ]
        ];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array
     */
    public function with($request): array
    {
        return [
            'links' => auth()->user()->getMenuLinks()
        ];
    }
}
