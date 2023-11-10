<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Multicaret\Acquaintances\Traits\CanLike;
use Multicaret\Acquaintances\Traits\Friendable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use Friendable, CanLike;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
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
        'password' => 'hashed',
    ];
    // Assigning profile's title to username
    public static function boot()
    {
        parent::boot();
        static::created(function ($user) {
            $user->profile()->create([
                'title' => $user->username,
                'image' => 'users-avatar/anon.png',
            ]);
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'DESC');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
    }
    public function following()
    {
        return $this->belongsToMany(Profile::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'participant_1')
            ->orWhere('participant_2', $this->id);
    }
}
