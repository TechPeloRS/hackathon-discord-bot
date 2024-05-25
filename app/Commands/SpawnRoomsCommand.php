<?php

namespace App\Commands;

use App\Actions\Teams\Spawn\SpawnTeamAction;
use App\Models\Guild;
use App\Models\Team\Team;
use Discord\Parts\User\Member;
use Illuminate\Database\Eloquent\Builder;
use Laracord\Commands\Command;
use Laracord\Discord\Message;
use function React\Async\await;

class SpawnRoomsCommand extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'spawn';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'The spawn-rooms-command command.';

    /**
     * Determines whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = false;

    /**
     * Determines whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the command.
     *
     * @param \Discord\Parts\Channel\Message $message
     * @param array $args
     * @return void
     */

    public function handle($message, $args)
    {
        return;
        $discord = app('bot')->discord();

        /** @var Guild $guild */
        $guild = $discord->guilds->get('id', config('bot.main_guild'));

        $teams = Team::whereNotNull('guild_id')->get();

        foreach ($guild->members as $member) {
            /** @var Member $member */
            dump("dm sent to: " . $member->username);

            $member->sendMessage(
                Message::make(null)
                    ->title("Aviso de migração de servidores")
                    ->info()
                    ->content("
                        Olá participante da Maratona Tech pelo RS.

                        Realizamos uma manutenção no nosso servidor de Discord e migramos todos os canais de equipes para nosso servidor principal, com o objetivo de facilitar e centralizar a comunicação com todas as equipes, bem como o atendimento de mentoria.

                        Por favor, pedimos que caso você tenha alguma dificuldade ou faltar alguém da sua equipe, abra um ticket no canal #suporte e iremos te ajudar.

                        Agradecemos a sua paciência e dedicação até aqui!

                        --------------------------------------------------------------------
                        Nome do servidor principal: Maratona Tech pelo RS
                        Link para abrir um chamado de suporte: https://discord.gg/EtXtHg74
                        --------------------------------------------------------------------
                    ")
                    ->build()
            );
        }

        $this
            ->message()
            ->title('SpawnRoomsCommand')
            ->content('Updated')
            ->send($message);
    }

    public function handleRooms($message, $args)
    {
        $discord = app('bot')->discord();

        $guild = $discord->guilds->get('id', config('bot.main_guild'));

        $teams = Team::whereNotNull('guild_id')->get();

        foreach ($teams as $team) {

            $hasTeamRole = $guild->roles->find(fn($role) => $role->id === $team->role_id);

            if ($hasTeamRole) {
                dump('Role already exists: ' . $team->id);
                continue;
            }

            dump('Role Missing: ' . $team->id);
            app(SpawnTeamAction::class)->handle($team);

            $teamMembers = $team->members;
            foreach ($teamMembers as $teamMember) {
                $member = $guild->members->get('id', $teamMember->discord_id);
                $this->addParticipantRoles($member);
            }
            dump('Team added: ' . $team->id);


        }

        $this
            ->message()
            ->title('SpawnRoomsCommand')
            ->content('Hello world!')
            ->send($message);
    }


    private function addParticipantRoles(Member $member): void
    {
        $teamMember = \App\Models\Team\Member::query()
            ->where('discord_id', $member->id)
            ->first();

        $hasRole = $member->roles->find(fn($role) => $role->id === $teamMember->team->role_id);

        if ($hasRole) {
            return;
        }

        try {
            await($member->addRole($teamMember->team->role_id));
        } catch (\Exception $e) {
            dump($e->getMessage());
        }

    }
}
