<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['image'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }

    public static function createByFile(string $path): Image
    {
        return self::query()
            ->create([
                'image' => file_get_contents(($path))
            ])->getModel();
    }

    public function updateByFile(string $path): bool
    {
        return $this->update([
            'image' => file_get_contents(($path))
        ]);
    }
}
