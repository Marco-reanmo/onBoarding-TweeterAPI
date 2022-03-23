<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{

    public static $wrap = 'image';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $path = 'public/image' . $this->id . '.png';
        Storage::put($path, $this->image);
        return [
            'image_link' => Storage::url($path),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
