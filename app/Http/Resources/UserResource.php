<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'forename' => $this->forename,
                'surname' => $this->surname,
                'profile_picture' => new ImageResource($this->whenLoaded('profile_picture'))
            ],
            'links' => [
                'profile' => 'api/users/' . $this->uuid,
                'toggle-follow' => 'api/users/' . $this->uuid . '/toggle-follow',
            ]
        ];
    }

}
