<?php

namespace App\Services\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    public function __invoke(Model $model) {
        $imgId = $model->getAttribute('image_id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
    }
}
