<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $receiver;
    protected $way = 1;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver, $way, ChatMessage $message)
    {
        $this->receiver = $receiver;
        $this->way = $way;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastAs()
    {
        if($this->way == 1){
            return 'MessageReceive-v'.$this->receiver;
        } else {
            return 'MessageReceive-u'.$this->receiver;
        }
    }
    
    public function broadcastOn()
    {
        return new Channel('chat-list');
        // return new PrivateChannel('chat');
    }
}
