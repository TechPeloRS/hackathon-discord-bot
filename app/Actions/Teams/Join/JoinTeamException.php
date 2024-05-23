<?php

namespace App\Actions\Teams\Join;



use App\Exceptions\CommandException;

class JoinTeamException extends CommandException
{

    public static function alreadyInATeam(): self
    {
        return new self('Você já está em um time! Caso deseje trocar de time, contate um organizador.');
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
