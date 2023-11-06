<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Multicaret\Acquaintances\Traits\CanBeLiked;

class Post extends Model
{
    use HasFactory;
    use CanBeLiked;
    protected $fillable = [
        'caption',
        'image',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
