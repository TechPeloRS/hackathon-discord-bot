<?php

namespace App\Actions\Teams\Join;

use App\Models\Guild;
use App\Models\Team\Member as TeamMember;
use App\Models\Team\Team;
use Discord\Parts\Channel\Channel;
use function React\Async\await;

class JoinTeamAction
{
    private array $newChannels = [
        [
            'name' => 'geral',
            'category' => Channel::TYPE_TEXT
        ],
//        [
//            'name' => 'links-uteis',
//            'category' => Channel::TYPE_TEXT
//        ],
        [
            'name' => 'Voz',
            'category' => Channel::TYPE_VOICE
        ],
    ];

    public function __construct(
        private readonly Team       $team,
        private readonly TeamMember $teamMember
    )
    {
    }

    public function handle(JoinTeamDTO $dto): Team
    {
        $memberIsAlreadyInATeam = $this->teamMember->alreadyJoinedATeam($dto->member->id);
        if ($memberIsAlreadyInATeam) {
            throw JoinTeamException::alreadyInATeam();
        }

        $team = $this->team->findByOwnerEmail($dto->teamKey);

        if (!$team) {
            throw JoinTeamException::teamCodeNotExists($dto);
        }


        if (!$team->guild_id) {
            $this->setGuildToTeam($team);
        }

        $team->addMember($dto);
        $this->manageRoles($dto);

        return $team;
    }

    private function manageRoles(JoinTeamDTO $dto): void
    {
        $teamlessRole = $dto->member->guild->roles->find(fn($role) => $role->name === 'Sem Time');
        $teamedRole = $dto->member->guild->roles->find(fn($role) => $role->name === 'Em Time');

        $hasTeamRole = $dto->member->roles->find(fn($role) => $role->name === 'Em Time');
        if (!$hasTeamRole) {
            await($dto->member->addRole($teamedRole));
        }

        $hasTeamlessRole = $dto->member->roles->find(fn($role) => $role->name === 'Sem Time');
        if ($hasTeamlessRole) {
            await($dto->member->removeRole($teamlessRole));
        }

    }

    private function setGuildToTeam(Team $team): void
    {
        $guild = Guild::query()
            ->where('main_server', false)
            ->where('teams_count', '<', config('bot.teamsPerGuild'))
            ->first();

        $team->update(['guild_id' => $guild->provider_id]);
    }

}
