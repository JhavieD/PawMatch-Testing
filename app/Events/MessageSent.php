<?php

namespace App\Events;

use App\Models\Shared\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [

            'message_id' => $this->message->message_id,
            'message_content' => $this->message->message_content,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'sent_at' => $this->message->sent_at->timezone('Asia/Manila')->toIso8601String(),
        ];
    }
}
