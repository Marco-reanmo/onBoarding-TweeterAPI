<?php

namespace App\Services\Storage;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    public function __invoke(Image $image) {
        $imgId = $image->getAttribute('id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
    }
}
