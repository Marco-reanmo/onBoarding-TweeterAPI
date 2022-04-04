<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['follower_id', 'followed_id'];

    public function followers() {
        $this->hasMany('users', 'id', 'followed_id');
    }

    public function followedBy() {
        $this->belongsTo('users', 'id', 'follower_id');
    }
}
