<?php

namespace App\Discord\Events;

use App\Discord\Commands\CommandsEnum;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

class SlashCommands
{
    public function setup(Discord $discord): void
    {
        $this->registerCommands($discord);
        $this->registerActions($discord);
    }

    private function registerCommands(Discord $discord): void
    {
        foreach (CommandsEnum::cases() as $command) {
            $command = new Command($discord, [
                'name' => $command->value,
                'description' => $command->getDescription(),
                'options' => $command->getOptions()
            ]);

            $discord
                ->application
                ->commands
                ->save($command)->done(function (Command $command) {
                    echo "Command {$command->name} registered", PHP_EOL;
                });
        }
    }

    private function registerActions(Discord $discord): void
    {
        $discord->listenCommand(
            CommandsEnum::Ping->value,
            fn (Interaction $interaction) => CommandsEnum::Ping->getAction()->handle($discord, $interaction)
        );

        $discord->listenCommand(
            CommandsEnum::Test->value,
            fn (Interaction $interaction) => CommandsEnum::Test->getAction()->handle($discord, $interaction)
        );
    }

}
