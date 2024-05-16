<?php

namespace App\Discord\Events;

use App\Discord\Commands\CommandException;
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
                'options' => $command->getOptions(),
                'default_member_permissions' => $command->getPermissions()
            ]);

            $discord
                ->application
                ->commands
                ->save($command)->done(fn(Command $command) => dump("Command {$command->name} registered"));
        }
    }

    private function registerActions(Discord $discord): void
    {
        foreach (CommandsEnum::cases() as $command) {
            $discord->listenCommand(
                $command->value,
                function (Interaction $interaction) use ($discord, $command){
                    try {
                        $command->getAction()->handle($discord, $interaction);
                    } catch (CommandException $ex) {
                        $interaction->respondWithMessage($ex->buildErrorMessage());
                    }
                }
            );
        }
    }

}
