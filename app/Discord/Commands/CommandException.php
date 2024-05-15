<?php

namespace App\Discord\Commands;

use Discord\Builders\MessageBuilder;
use Exception;

abstract class CommandException extends Exception
{
    public function buildErrorMessage(): MessageBuilder
    {
        return MessageBuilder::new()
            ->setContent('Ih fi deu ruim: ' . $this->message);

    }
}
