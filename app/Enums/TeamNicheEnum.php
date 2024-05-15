<?php

namespace App\Enums;

enum TeamNicheEnum: string
{
    case Unknown = 'unknown';
    case Pre = 'pre_disaster';
    case During = 'during_disaster';
    case Post = 'post_disaster';

    public function getDescription(): string
    {
        return match ($this) {
            self::Pre => 'Pre Disaster',
            self::During => 'During Disaster',
            self::Post => 'Post Disaster',
        };
    }
}
