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
            'forename' => $this->forename,
            'surname' => $this->surname,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile_picture' => new ImageResource($this->whenLoaded('profile_picture'))
        ];
    }

    public function with($request)
    {
        return [
            'links' => [
                'show' => '/users/' . $this->id,
                'follow' => '/users/' . $this->id . '/follow'
            ]
        ];
    }
}
