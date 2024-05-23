<?php

namespace App\Actions\Mentors;

use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\Member;
use Discord\Repository\Interaction\OptionRepository;

readonly class MentorDTO
{
    public function __construct(
        public Guild  $guild,
        public Member $member,
        public OptionRepository $args
    )
    {
    }

    public static function makeFromInteraction(Interaction $interaction): self
    {
        return new self(
            guild: $interaction->guild,
            member: $interaction->member,
            args: $interaction->data->options->first()->options
        );
    }
}
