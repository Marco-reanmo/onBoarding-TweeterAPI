<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'parent_id',
        'image_id',
        'body'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }

    public function allParents(): BelongsTo
    {
        return $this->parent()->with('allParents');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Tweet::class, 'parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->comments()->with(['allComments.author.profile_picture', 'allComments.image']);
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getCommentCount(): int
    {
        $allComments = $this->allComments()->get();
        $count = $allComments->count();
        foreach ($allComments as $comment) {
            $count += $comment->getCommentCount();
        }
        return $count;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? false, function($query) use($filters) {
            $search = $filters['search'];
            $query->whereHas('author', function (Builder $query) use ($search) {
                $query->where('forename', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            })->orWhere('body', 'like', '%' . $search . '%');
        });
        $query->when($filters['user'] ?? false, function ($query) use ($filters) {
            $user = $filters['user'];
            $query->whereHas('author', function (Builder $query) use ($user) {
                $query->where('uuid', '=', $user);
            });
        });
    }

    public function hasImage(): bool
    {
        return $this->getAttribute('image_id') != null;
    }
}
