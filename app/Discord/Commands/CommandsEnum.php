<?php

namespace App\Discord\Commands;

use App\Discord\Events\SlashCommands;
use Discord\Parts\Interactions\Command\Command;

enum CommandsEnum: string
{
    case Ping = 'ping';
    case Test = 'test';

    public function getDescription(): string
    {
        return match ($this) {
            self::Ping => 'Replies with Pong!',
            self::Test => 'Test Purposes',
        };

    }

    public function getAction(): CommandInterface
    {
        return match ($this) {
            self::Test => new TestCommand(),
            self::Ping => new PingCommand(),
        };
    }

    public function getOptions(): array
    {
        return match ($this) {
            self::Test => [
                [
                    'name' => 'test',
                    'description' => 'Test Option',
                    'type' => Command::MESSAGE,
                    'required' => true
                ]
            ],
            self::Ping => [
                [
                    'name' => 'test',
                    'description' => 'Test Option',
                    'type' => Command::MESSAGE,
                    'required' => true
                ]
            ]
        };
    }

}
