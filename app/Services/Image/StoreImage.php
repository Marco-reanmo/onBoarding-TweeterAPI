<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;

class StoreImage
{
    public function __invoke(string $imagePath, Model $model): Image
    {
        $imageAttributes = [
            'image' => file_get_contents($imagePath),
            'imageable_id' => $model->getAttribute('id'),
            'imageable_type' => $model::class
        ];
        return Image::query()
            ->create($imageAttributes);
    }
}
