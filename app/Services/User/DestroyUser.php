<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DestroyUser
{
    public function __invoke(User $user) {
        $imgId = $user->getAttribute('image_id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $user->delete();
    }
}
