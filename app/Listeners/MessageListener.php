<?php

namespace App\Listeners;

use App\Events\Message as MessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Message;
use Exception;

class MessageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageEvent $event): void
    {
        try {
            Message::create([
                'sender' => $event->sender,
                'conversation_id' => $event->conversationId,
                'message' => $event->message
            ]);
        } catch (Exception $th) {
            logger($th);
        }
    }
}
