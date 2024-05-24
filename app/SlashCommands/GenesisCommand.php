<?php

namespace App\SlashCommands;

use App\Actions\Teams\Spawn\SpawnTeamAction;
use App\Models\Team\Team;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;
use Illuminate\Database\Eloquent\Collection;
use Laracord\Commands\SlashCommand;
use function React\Async\await;

class GenesisCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'iniciar-hacka';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Boa sorte pra todo mundo!';

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
    protected $hidden = true;


    public function handle($interaction)
    {
        $spawnTeam = app(SpawnTeamAction::class);

        $interaction->respondWithMessage(MessageBuilder::new()
            ->setContent("Hackathon Iniciado!")
        );


        if (in_array($interaction->member->id, config('discord.admins') )) {
            $this->wipeRoles();
            $this->wipeChannels();
            dump('foi');
        }

        return;
        /** @var Team $team */

        Team::query()->chunk(
            config('bot.teamsPerGuild'),
            fn(Collection $teams) => $teams->each(fn(Team $team) => $spawnTeam->handle($team))
        );

    }

    private function wipeChannels(): void
    {
        $guilds = $this->discord()->guilds->getIterator();

        /** @var Guild $guild */
        foreach ($guilds as $guild) {
            $channels = $guild->channels
                ->filter(fn(Channel $channel) => str($channel->name)->startsWith(['geral', 'links-uteis', 'Voz', 'Time']))
                ->getIterator();
            foreach ($channels as $channel) {
                $guild->channels->delete($channel);
            }
        }

    }

    private function wipeRoles(): void
    {
        $guilds = $this->discord()->guilds->getIterator();

        /** @var Guild $guild */
        foreach ($guilds as $guild) {
            $roles = $guild->roles
                ->filter(fn(Role $role) => str($role->name)->startsWith('Time'))
                ->getIterator();
            foreach ($roles as $role) {
                await($guild->roles->delete($role));
            }
        }
    }
}
