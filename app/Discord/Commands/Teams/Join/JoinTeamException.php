<?php

namespace App\Discord\Commands\Teams\Join;

use App\Discord\Commands\CommandException;

class JoinTeamException extends CommandException
{

    public static function alreadyInATeam(): self
    {
        return new self('Você já está em um time!');
    }

    public static function teamCodeNotExists(JoinTeamDTO $dto): self
    {
        return new self(sprintf('O código de time %s não existe!', $dto->teamKey));
    }

    public static function teamAlreadyFull(): self
    {
        return new self('Esse time já está cheio!');
    }
}
