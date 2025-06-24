<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Add your table/fields/relationships here

    protected $primaryKey = 'message_id';
    protected $table = 'messages';
    public $timestamps = true;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message_content',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(\App\Models\Shared\User::class, 'sender_id', 'user_id');
    }
    public function receiver()
    {
        return $this->belongsTo(\App\Models\Shared\User::class, 'receiver_id', 'user_id');
    }
}