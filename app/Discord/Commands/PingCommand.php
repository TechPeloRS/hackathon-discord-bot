<?php

namespace App\Discord\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class PingCommand implements CommandInterface
{
    public function handle(Discord $discord, Interaction $interaction): void
    {
        // $interaction->data->options->map(fn ($option) => [$option->name, $option->value])
        $interaction->respondWithMessage(MessageBuilder::new()->setContent('Pong!'));
    }
}
