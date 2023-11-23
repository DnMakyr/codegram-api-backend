<?php

namespace App\Models;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['sender', 'conversation_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender');
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}