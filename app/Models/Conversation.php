<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function participant_1(){
        return $this->belongsTo(User::class, 'participant_1');
    }
    public function participant_2(){
        return $this->belongsTo(User::class, 'participant_2');
    }
}