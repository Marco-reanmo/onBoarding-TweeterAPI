<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'imageable_id',
        'imageable_type'
    ];

    /**
     * @return MorphTo
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Updates an image-model by a file.
     *
     * @param string $path
     * @return bool
     */
    public function updateByFile(string $path): bool
    {
        return $this->update([
            'image' => file_get_contents(($path))
        ]);
    }
}
