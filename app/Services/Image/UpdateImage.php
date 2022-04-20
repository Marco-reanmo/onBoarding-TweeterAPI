<?php

namespace App\Services\Image;

use App\Models\Image;

class UpdateImage
{
    public function __invoke(string $imagePath, Image $image) {
        $image->update(
            ['image' => file_get_contents($imagePath)]
        );
    }
}
