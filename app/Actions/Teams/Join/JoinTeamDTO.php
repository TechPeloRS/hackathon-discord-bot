<?php

namespace App\Actions\Teams\Join;

use App\Enums\TeamRoleEnum;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\Member;

readonly class JoinTeamDTO
{
    public function __construct(
        public Member       $member,
        public TeamRoleEnum $selectedMemberRoleType,
        public string       $teamKey,
        public ?string       $githubUsername,
    )
    {

    }

    public static function makeFromInteraction(Interaction $interaction): self
    {
        return new self(
            member: $interaction->member,
            selectedMemberRoleType: TeamRoleEnum::from($interaction->data->options->pull('area')->value),
            teamKey: $interaction->data->options->pull('chave')->value,
            githubUsername: $interaction->data->options->pull('github')?->value,
        );

    }
}
