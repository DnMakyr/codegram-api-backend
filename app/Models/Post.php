<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;
use Maize\Markable\Models\Reaction;
use Multicaret\Acquaintances\Traits\CanBeLiked;

class Post extends Model
{   
    use HasFactory;
    protected $fillable = [
        'caption',
        'image',
    ];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }
    // protected static array $mark = [
    //     Like::class,
    //     Reaction::class,

    // ];
}