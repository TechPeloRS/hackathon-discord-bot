<?php

namespace App\Actions\Mentors\Join;

use App\Exceptions\CommandException;

class MentorJoinException extends CommandException
{
    public static function notFound(): self
    {
        return new self('Sua inscrição como mentor não foi encontrada! Por favor, entre em contato com a organização.');
    }

    public static function alreadyAccepted(): self
    {
        return new self('Você já está inscrito como mentor.');
    }
}
