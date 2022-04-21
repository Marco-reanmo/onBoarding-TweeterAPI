<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Image
 */
class ImageResource extends JsonResource
{

    public static $wrap = 'image';

    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        $path = 'public/images/image' . $this->id . '.png';
        Storage::put($path, $this->image);
        return [
            'image_link' => Storage::url($path),
        ];
    }
}
