<?php

namespace App\Enums;

enum TeamRoleEnum: string
{
    case Developer = 'developer';
    case Artist = 'artist';
    case Business = 'business';
    case Engineer = 'engineer';
    case Marketing = 'marketing';
    case HR = 'hr';
    case Logistics = 'logistics';


    public function getDescription(): string
    {
        return match($this) {
            self::Developer => 'Pesssoa Desenvolvedora',
            self::Artist => 'Pessoa Artista',
            self::Business => 'Pessoa de Negócios',
            self::Engineer => 'Pessoa Engenheira',
            self::Marketing => 'Pessoa de Marketing',
            self::HR => 'Pessoa de Recursos Humanos',
            self::Logistics => 'Pessoa de Logística/Mobilidade',
        };
    }

    public function getDiscordId(): string
    {
        return match ($this) {
            self::Developer => '123456789',
            self::Artist => throw new \Exception('To be implemented'),
            self::Business => throw new \Exception('To be implemented'),
            self::Engineer => throw new \Exception('To be implemented'),
            self::Marketing => throw new \Exception('To be implemented'),
            self::HR => throw new \Exception('To be implemented'),
            self::Logistics => throw new \Exception('To be implemented'),

        };

    }
}
