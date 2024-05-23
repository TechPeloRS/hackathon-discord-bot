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
        return match ($this) {
            self::Developer => 'Pessoa Desenvolvedora',
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
            self::Developer => '1241469983188582441',
            self::Business => '1241470085160243351',
            self::Engineer => '1241470066361634956',
            self::Marketing, self::Artist => '1241470339699970088',
            self::HR => '1241470768391393320',
            self::Logistics => '1241470469945692301',
        };

    }
}
