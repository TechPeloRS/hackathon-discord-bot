<?php

namespace App\Actions\Mentors\Help;

use App\Exceptions\CommandException;

class MentorHelpException extends CommandException
{
    public static function notRegistered(): self
    {
        return new self('Você não está registrado como partipante. Entre em um time e tente novamente!');
    }
}
