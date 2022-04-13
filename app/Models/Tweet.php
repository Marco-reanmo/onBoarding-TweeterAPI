<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Pagination\Paginator;
use LaravelIdea\Helper\App\Models\_IH_Tweet_C;

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
        return $this->belongsTo(Tweet::class)->with('parent');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Tweet::class, 'parent_id')->with('comments');
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function getCommentCount(): int
    {
        $allComments = $this->comments()->withCount('comments')->get();
        $count = $allComments->count();
        foreach ($allComments as $comment) {
            $count += $comment->comments_count;
        }
        return $count;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function scopeFilter($query, array $filters) {
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

    public function hasImage(): bool
    {
        return $this->getAttribute('image_id') != null;
    }

    public static function getByIds(array $ids): Paginator|array|_IH_Tweet_C
    {
        return self::with(['image', 'author.profile_picture'])
            ->whereIn('user_id', $ids)
            ->where('parent_id', '=', null)
            ->withCount('usersWhoLiked')
            ->filter(request(['search', 'user']))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10)
            ->withQueryString();
    }

    public static function getNewsfeedFor(User $user): Paginator|array|_IH_Tweet_C
    {
        $relevantIds = $user->getFollowedIds();
        $relevantIds[] = $user->getAttribute('id');
        return self::getByIds($relevantIds);
    }
}
