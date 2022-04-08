<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\Paginator;
use Laravel\Sanctum\HasApiTokens;
use LaravelIdea\Helper\App\Models\_IH_Tweet_C;

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

    public function profile_picture(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
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

    public function verificationToken(): BelongsTo
    {
        return $this->belongsTo(VerificationToken::class);
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
            $query->where(function($query) use($search) {
                $query->where('forename', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            });
        });
    }


    public function hasProfilePicture() : bool
    {
        return $this->getAttribute('image_id') != null;
    }

    public function isSameUserAs(User $model) : bool
    {
        return $this->getAttribute('id') === $model->getAttribute('id');
    }

    public function getMenuLinks() {
        return [
            'home' => 'api/tweets',
            'myTweets' => 'api/tweets?user=' . $this->getAttribute('uuid'),
            'settings' => 'api/users/' . $this->getAttribute('uuid')
        ];
    }

    public static function getByEmail(string $email): User
    {
        return self::query()
            ->firstWhere('email', '=', $email);
    }

    public function getTweets(): Paginator|array|_IH_Tweet_C
    {
        $relevantIds = $this->followed()->pluck('users.id');
        $relevantIds[] = $this->getAttribute('id');
        return Tweet::with(['image', 'author.profile_picture'])
            ->whereIn('user_id', $relevantIds)
            ->where('parent_id', '=', null)
            ->filter(request(['search', 'user']))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10)
            ->withQueryString();
    }
}
