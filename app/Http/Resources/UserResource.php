<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{

    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'forename' => $this->forename,
                'surname' => $this->surname,
                'profile_picture' => new ImageResource($this->whenLoaded('profilePicture'))
            ],
            'links' => [
                'profile' => 'api/users/' . $this->uuid,
                'toggle-follow' => 'api/users/' . $this->uuid . '/toggle-follow',
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
