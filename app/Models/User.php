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

    public function profile_picture(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function followedBy(): BelongsToMany
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
        $query->when($filters['search'] ?? false, fn($query, $search) =>
        $query->where(fn($query)=>
        $query->where('forename', 'like', '%' . request('search') . '%')
            ->orWhere('surname', 'like', '%' . request('search') . '%')
        )
        );
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
}
