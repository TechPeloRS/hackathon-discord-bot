<?php

namespace App\Discord\Commands;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

interface CommandInterface
{
    public function handle(Discord $discord, Interaction $interaction): void;
}
