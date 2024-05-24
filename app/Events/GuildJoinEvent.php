<?php

namespace App\Events;

use App\Models\Guild;
use App\Models\Mentor\Mentor;
use Discord\Discord;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event as Events;
use Illuminate\Database\Eloquent\Builder;
use Laracord\Events\Event;
use function React\Async\await;

class GuildJoinEvent extends Event
{
    /**
     * The event handler.
     *
     * @var string
     */
    protected $handler = Events::GUILD_MEMBER_ADD;

    /**
     * Handle the event.
     * @return void
     */
    public function handle(Member $member, Discord $discord)
    {
        if (Guild::query()->where('provider_id', $member->guild_id)->doesntExist()) {
            return;
        }
        $this->addTeamlessRole($member);
        $this->addParticipantRoles($member);
        $this->addMentorRole($member);
    }

    private function addTeamlessRole(Member $member)
    {
        if ($member->guild_id === config('bot.main_guild')) {
            return;
        }

        $hasRole = $member->roles->find(fn($role) => $role->name === 'Sem Time');

        if ($hasRole) {
            return;
        }

        await($member->addRole(
            $member->guild->roles->find(fn($role) => $role->name === 'Sem Time')
        ));
    }

    private function addParticipantRoles(Member $member): void
    {
        $hasGuild = Guild::query()
            ->where('main_server', false)
            ->where('provider_id', $member->guild_id)
            ->exists();

        if (!$hasGuild) {
            return;
        }

        $teamMember = \App\Models\Team\Member::query()
            ->where('discord_id', $member->id)
            ->whereHas('team', fn(Builder $team) => $team->where('guild_id', $member->guild_id))
            ->first();

        if (!$teamMember) {
            dump("Member {$member->id} not found in any team");
            return;
        }

        await($member->addRole($teamMember->team->role_id));

    }

    private function addMentorRole(Member $member): void
    {
        if ($member->guild_id === config('bot.main_guild')) {
            return;
        }

        $mentor = Mentor::query()
            ->where('provider_id', $member->id)
            ->first();

        if (!$mentor) {
            return;
        }

        await($member->addRole(
            $member->guild->roles->find(fn($role) => $role->name === 'Pessoa Mentora')
        ));
    }
}
