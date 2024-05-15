<?php

namespace App\Discord\Commands\Teams\Join;

use App\Discord\Commands\CommandInterface;
use App\Enums\TeamRoleEnum;
use App\Models\Team\Member as TeamMember;
use App\Models\Team\Team;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Permissions\ChannelPermission;
use Discord\Parts\User\Member;

class JoinTeamCommand implements CommandInterface
{
    public function __construct(
        private readonly Team       $team,
        private readonly TeamMember $teamMember
    )
    {
    }

    public function handle(Discord $discord, Interaction $interaction): void
    {
        $guild = $discord->guilds->first();
        $dto = JoinTeamDTO::makeFromInteraction($interaction);


        $memberIsAlreadyInATeam = $this->teamMember->alreadyJoinedATeam($dto->member->id);
        if ($memberIsAlreadyInATeam) {
            throw JoinTeamException::alreadyInATeam();
        }

        $team = $this->team->findByCode($dto->teamKey);

        if (!$team) {
            throw JoinTeamException::teamCodeNotExists($dto);
        }

        if ($team->hasMaxMembers()) {
            throw JoinTeamException::teamAlreadyFull();

        }

        $this->giveChannelsPermission($discord, $guild, $dto->member, $team);
        $team->addMember($dto);

        $interaction->respondWithMessage(MessageBuilder::new()
            ->setContent(sprintf('Você entrou no time %s!', $team->id))
        );
    }

    private function giveChannelsPermission(
        Discord $discord,
        Guild   $guild,
        Member  $member,
        Team    $team
    ): void
    {
        foreach ($team->channels_ids as $channelId) {

            $channel = $guild->channels->get('id', $channelId);
            $allow = new ChannelPermission($discord, [
                'view_channel' => true,
                'read_messages' => true,
                'send_messages' => true,
                'attach_files' => true,
                'connect' => true,
                'speak' => true,
                'add_reactions' => true
            ]);

            $deny = new ChannelPermission($discord, []);

            $overwrite = $channel->overwrites->create([
                'allow' => $allow,
                'deny' => $deny
            ]);

            $channel->setOverwrite($member, $overwrite)->done();
        }

    }
}
