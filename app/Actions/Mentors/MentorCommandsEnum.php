<?php

namespace App\Actions\Mentors;

use App\Actions\Mentors\Help\MentorHelpAction;
use App\Actions\Mentors\Join\MentorJoinAction;

enum MentorCommandsEnum: string
{
    case Join = 'entrar';
    case Help = 'pedir-ajuda';


    public function getAction(): MentorCommandInterface
    {
        return match ($this) {
            self::Join => app(MentorJoinAction::class),
            self::Help => app(MentorHelpAction::class)
        };
    }
}
