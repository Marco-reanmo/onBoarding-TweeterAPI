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

    /**
     * @return MorphOne
     */
    public function profilePicture(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * @return BelongsToMany
     */
    public function followed(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id');
    }

    /**
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id');
    }

    /**
     * @return HasMany
     */
    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    /**
     * @return BelongsToMany
     */
    public function likedTweets(): BelongsToMany
    {
        return $this->belongsToMany(Tweet::class, 'likes', 'user_id', 'tweet_id');
    }

    /**
     * @return HasOne
     */
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

    /**
     * Filters query by forename and surname.
     *
     * @param Builder $query
     * @param array $filters
     * @return Model|Builder|null
     */
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

    /**
     * Determines whether the User has a profile picture.
     *
     * @return bool
     */
    public function hasProfilePicture() : bool
    {
        return $this->profilePicture()->exists();
    }

    /**
     * Return Meta-Menu-Links related to the user.
     *
     * @return string[]
     */
    public function getMenuLinks(): array
    {
        return [
            'home' => 'api/tweets',
            'myTweets' => 'api/tweets?user=' . $this->getAttribute('uuid'),
            'settings' => 'api/users/' . $this->getAttribute('uuid')
        ];
    }

    /**
     * Returns the user with the given email.
     *
     * @param Builder $query
     * @param string $email
     * @return Model|Builder|null
     */
    public function scopeEmail(Builder $query, string $email): Model|Builder|null
    {
        return $query->firstWhere('email', $email);
    }

    /**
     * Returns the ids of the users that are followed by this user.
     *
     * @return Collection
     */
    public function getFollowedIds(): Collection
    {
        return $this->followed()->pluck('users.id');
    }

    /**
     * Returns and filters all users except this one.
     *
     * @param $search
     * @return Paginator
     */
    public function getOtherUsers($search): Paginator
    {
        return $this->with('profilePicture')
            ->whereNot('id', $this->getAttribute('id'))
            ->filter($search)
            ->simplePaginate(10)
            ->withQueryString();
    }

    /**
     * Returns all ids of followed users plus this user's id.
     *
     * @return array
     */
    public function getNewsfeedIds(): array
    {
        return $this->getFollowedIds()
            ->merge($this->getAttribute('id'))
            ->toArray();
    }
}
