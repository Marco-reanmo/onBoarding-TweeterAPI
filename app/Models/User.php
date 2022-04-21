<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'image_id',
        'forename',
        'surname',
        'email',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profilePicture(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function followed(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id');
    }

    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    public function likedTweets(): BelongsToMany
    {
        return $this->belongsToMany(Tweet::class, 'likes', 'user_id', 'tweet_id');
    }

    public function verificationToken(): HasOne
    {
        return $this->hasOne(VerificationToken::class);
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

    public function scopeFilter(Builder $query, array $filters): Model|Builder|null
    {
        return $query->when($filters['search'] ?? false, function(Builder $query) use($filters) {
            $search = $filters['search'];
            $query->where(function(Builder $query) use($search) {
                $query->where('forename', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            });
        });
    }

    public function hasProfilePicture() : bool
    {
        return $this->profilePicture()->exists();
    }

    public function getMenuLinks(): array
    {
        return [
            'home' => 'api/tweets',
            'myTweets' => 'api/tweets?user=' . $this->getAttribute('uuid'),
            'settings' => 'api/users/' . $this->getAttribute('uuid')
        ];
    }

    public function scopeEmail(Builder $query, string $email): Model|Builder|null
    {
        return $query->firstWhere('email', $email);
    }

    public function getFollowedIds(): Collection
    {
        return $this->followed()->pluck('users.id');
    }

    public function getOtherUsers($search): Paginator
    {
        return $this->with('profilePicture')
            ->whereNot('id', $this->getAttribute('id'))
            ->filter($search)
            ->simplePaginate(10)
            ->withQueryString();
    }

    public function getNewsfeedIds(): array
    {
        return $this->getFollowedIds()
            ->merge($this->getAttribute('id'))
            ->toArray();
    }
}
