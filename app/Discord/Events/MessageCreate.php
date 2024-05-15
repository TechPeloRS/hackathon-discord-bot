<?php

namespace App\Discord\Events;

use Discord\Discord;
use Discord\Parts\Channel\Message;

class MessageCreate
{
    public function handle(Message $message, Discord $discord)
    {
        echo "{$message->author->username}: {$message->content}", PHP_EOL;
    }
}
