<?php

namespace App\Http\Resources;

use App\Models\Tweet;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'tweet' => [
                'author' => new UserResource($this->whenLoaded('author')),
                'image' => new ImageResource($this->whenLoaded('image')),
                'body' => $this->body,
                'comments_count' => $this->getCommentCount(),
                'comments' => TweetResource::collection($this->whenLoaded('allComments')),
                'human_created_at' => $this->created_at->diffForHumans(),
                'like count' => $this->numberOfLikes()
            ],
            'links' => [
                'tweet' => 'api/tweets/' . $this->uuid,
                'toggle-like' => 'api/tweets/' . $this->uuid . '/like'
            ]
        ];
    }

    public function with($request): array
    {
        return [
            'links' => auth()->user()->getMenuLinks()
        ];
    }
}
