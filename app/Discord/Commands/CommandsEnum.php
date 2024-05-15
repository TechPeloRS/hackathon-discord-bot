<?php

namespace App\Discord\Commands;

use App\Discord\Commands\Admin\SetupChannelsCommand;
use App\Discord\Commands\General\PingCommand;
use App\Discord\Commands\Teams\Join\JoinTeamCommand;
use App\Enums\TeamRoleEnum;
use Discord\Parts\Interactions\Command\Command;

enum CommandsEnum: string
{
    case Ping = 'ping';
    case Test = 'test';

    case JoinTeam = 'entrar-time';

    public function getDescription(): string
    {
        return match ($this) {
            self::Ping => 'Replies with Pong!',
            self::Test => 'Test Purposes',
            self::JoinTeam => 'lalala teste'
        };
    }

    public function getPermissions(): int
    {
        return match ($this) {
            self::Ping, self::JoinTeam => (1 << 11), // can be used by everyone
            self::Test => 0 // only admins
        };
    }

    public function getAction(): CommandInterface
    {
        return match ($this) {
            self::Test => app(SetupChannelsCommand::class),
            self::Ping => app(PingCommand::class),
            self::JoinTeam => app(JoinTeamCommand::class),
        };
    }

    public function getOptions(): array
    {
        return match ($this) {
            self::Test => [],
            self::Ping => [
                [
                    'name' => 'test',
                    'description' => 'Test Option',
                    'type' => Command::MESSAGE,
                    'required' => true
                ]
            ],
            self::JoinTeam => [
                [
                    'name' => 'chave',
                    'description' => 'E-mail do lider ou código do time',
                    'type' => Command::MESSAGE,
                    'required' => true
                ],
                [
                    'name' => 'area',
                    'description' => 'Sua área de atuação',
                    'type' => Command::MESSAGE,
                    'required' => true,
                    'choices' => collect(TeamRoleEnum::cases())->map(fn(TeamRoleEnum $role) => [
                        'name' => $role->getDescription(),
                        'value' => $role->value
                    ])->toArray()
                ],
                [
                    'name' => 'github',
                    'description' => 'Seu usuário do GitHub, caso tenha! Ex: danielhe4rt',
                    'type' => Command::MESSAGE,
                    'required' => false
                ]
            ]
        };
    }

}
