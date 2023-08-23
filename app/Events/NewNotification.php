<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $data;
    public $user;

    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        // return new Channel('notifications-channel');
        return new Channel('user.' . $this->user->id);
        // return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'NotificationEvent';
    }

    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}
