<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'read_at',
        'sender_deleted_at',
        'receiver_deleted_at',
        'body',
        'conversation_id',
        'type'
    ];

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    public function isRead() : bool {
        return $this->read_at != null;
    }
    
 }
