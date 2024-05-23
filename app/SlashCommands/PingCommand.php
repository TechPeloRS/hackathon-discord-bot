<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;

class PingCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'ping-command';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'The ping-command slash command.';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The permissions required to use the command.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Indiciates whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = true;

    /**
     * Indicates whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the slash command.
     *
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return void
     */
    public function handle($interaction)
    {
        $message = $this
            ->message()
            ->content('Pong!')
            ->timestamp()
            ->build();
        $interaction->respondWithMessage($message, true);
    }
}
