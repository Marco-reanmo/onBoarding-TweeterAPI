<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    public function __invoke(Image $image) {
        $imgId = $image->getAttribute('id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $image->delete();
    }
}
