<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'user_requested_id',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
