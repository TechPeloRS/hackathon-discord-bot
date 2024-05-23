<?php

namespace App\Exceptions;

use Discord\Builders\MessageBuilder;
use Exception;
use Laracord\Discord\Message;

abstract class CommandException extends Exception
{
    public function buildErrorMessage(): MessageBuilder
    {
        return Message::make(null)
            ->title('Houve um erro ao processar seu comando!')
            ->error()
            ->field('Mensagem', $this->getMessage(), true)
            ->timestamp()
            ->build();
    }
}
