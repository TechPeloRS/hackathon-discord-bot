<?php

namespace App\SlashCommands;

use App\Actions\Teams\Join\JoinTeamAction;
use App\Actions\Teams\Join\JoinTeamDTO;
use App\Actions\Teams\Spawn\SpawnTeamAction;
use App\Enums\TeamRoleEnum;
use App\Exceptions\CommandException;
use App\Models\Team\Team;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Invite;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Command\Command;
use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use function React\Async\await;

class JoinTeamCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'entrar-time';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'The enter-team slash command.';

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
    protected $admin = false;

    /**
     * Indicates whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;


    public function handle($interaction)
    {
        $joinTeam = app(JoinTeamAction::class);
        $spawnTeam = app(SpawnTeamAction::class);

        try {
            $message = DB::transaction(function () use ($interaction, $joinTeam, $spawnTeam) {
                $dto = JoinTeamDTO::makeFromInteraction($interaction);
                $team = $joinTeam->handle($dto)->refresh();

                if (!$this->hasChannelsAndRoles($team)) {
                    dump("generating channels for team - {$team->id}...");
                    $spawnTeam->handle($team);
                } else {
                }

                $invite = $this->getInvite($team);
                return $this
                    ->message()
                    ->title('Olá Participante!')
                    ->content("Você entrou no time {$team->id}! Use o link para entrar no canal: https://discord.gg/{$invite->code}")
                    ->build();
            });

        } catch (CommandException $e) {
            $message = $e->buildErrorMessage();
        }
        $interaction->respondWithMessage($message, true);
    }

    private function getInvite(Team $team): Invite
    {
        $team = $team->refresh();
        $channelId = collect($team->channels_ids)->reverse()->first();
        $channel = $this->discord
            ->guilds
            ->find(fn(Guild $guild) => $guild->id == $team->guild_id)
            ->channels
            ->get('name', 'avisos');


        $inviteDTO = $channel->invites->create([
            'max_age' => 36000,
            'max_uses' => 100,
            'temporary' => false,
        ]);

        return await($channel->invites->save($inviteDTO));
    }

    public function options(): array
    {
        return [
            [
                'name' => 'chave',
                'description' => 'E-mail do lider ou código do time',
                'type' => Command::MESSAGE,
                'required' => true
            ],
            [
                'name' => 'area',
                'description' => 'Sua área de atuação',
                'type' => Command::MESSAGE,
                'required' => true,
                'choices' => collect(TeamRoleEnum::cases())->map(fn(TeamRoleEnum $role) => [
                    'name' => $role->getDescription(),
                    'value' => $role->value
                ])->toArray()
            ],
            [
                'name' => 'github',
                'description' => 'Seu usuário do GitHub, caso tenha! Ex: danielhe4rt',
                'type' => Command::MESSAGE,
                'required' => false
            ]
        ];
    }

    private function hasChannelsAndRoles(Team $team): bool
    {
        // TODO: gerar time
        $guild = $this->discord()
            ->guilds
            ->get('id', $team->guild_id);

        $hasChannels = $guild
            ->channels
            ->filter(fn($role) => in_array($role->id, $team->channels_ids))
            ->count();


        $hasRole = $guild
            ->roles
            ->filter(fn($role) => $role->id == $team->role_id)
            ->count();

        return $hasChannels && $hasRole;
    }
}
