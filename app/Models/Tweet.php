<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Pagination\Paginator;

class Tweet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'parent_id',
        'image_id',
        'body'
    ];

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tweet::class)->with('parent');
    }

    /**
     * Returns all comments including comments of those comments with images and the author's data.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Tweet::class, 'parent_id')
            ->with([
                'comments.image',
                'comments.author.profilePicture',
            ])->withCount([
                'usersWhoLiked'
            ]);
    }

    /**
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function usersWhoLiked(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'tweet_id', 'user_id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Filters query by forename, surname and body or by author's uuid.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter(Builder $query, array $filters) {
        $query->when($filters['search'] ?? false, function($query) use($filters) {
            $search = $filters['search'];
            $query->whereHas('author', function(Builder $query) use($search) {
                $query->where('forename', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            })->orWhere('body', 'like', '%' . $search . '%');
        });
        $query->when($filters['user'] ?? false, function($query) use($filters) {
            $user = $filters['user'];
            $query->whereHas('author', function(Builder $query) use($user) {
                $query->where('uuid', '=', $user);
            });
        });
    }

    /**
     * Determines whether a tweet has an image.
     *
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->image->exists();
    }

    /**
     * Returns and filters the tweets with images and the referring authors' data on top-level (without comments).
     *
     * @param Builder $query
     * @param array $relevantIds
     * @return Paginator
     */
    public function scopeNewsfeedFor(Builder $query, array $relevantIds): Paginator
    {
        return $query->with(['image', 'author.profilePicture'])
            ->whereIn('user_id', $relevantIds)
            ->whereDoesntHave('parent')
            ->filter(request(['search', 'user']))
            ->latest()
            ->simplePaginate(10)
            ->withQueryString();
    }
}
