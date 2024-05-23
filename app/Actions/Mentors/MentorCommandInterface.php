<?php

namespace App\Actions\Mentors;

use Discord\Builders\MessageBuilder;

/**
 * @throw CommandException
 */
interface MentorCommandInterface
{

    public function handle(MentorDTO $dto): MessageBuilder;
}
