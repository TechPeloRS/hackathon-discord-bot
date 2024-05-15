<?php

namespace App\Discord\Commands;

use App\Models\Team\Team;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class PingCommand implements CommandInterface
{
    public function handle(Discord $discord, Interaction $interaction): void
    {
        dump(Team::with('members')->get()->toArray());
        $interaction->respondWithMessage(MessageBuilder::new()->setContent('Pong!'));
    }
}
