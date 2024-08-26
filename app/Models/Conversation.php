<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    public $receiver = null;
    protected $fillable = [
        'receiver_id',
        'sender_id'
    ];

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)
                  ->where('receiver_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)
                  ->where('receiver_id', $user1Id);
        });
    }
    
    public function getReceiver() {
        if($this->receiver) return $this->receiver;
        
        if($this->sender_id === auth()->id()) {
            $this->receiver =  User::find($this->receiver_id);
        }        
        else {
            $this->receiver =  User::find($this->sender_id);
        }

        return $this->receiver;
    }

    public function getNewestMsg() {
        return $this->messages()->latest()->first();
    }

    public function countUnReadMessages() {
        return $this->messages()->where('receiver_id',auth()->id())
                                ->whereNull('read_at')
                                ->count();
    }
    
}
