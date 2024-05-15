<?php

namespace App\Models\Team;

use App\Enums\TeamNicheEnum;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'code',
        'owner_email',
        'niche_type',
        'members_count',
        'channels_ids',
    ];

    protected function casts()
    {
        return [
            'niche_type' => TeamNicheEnum::class,
            'channels_ids' => 'array',
        ];
    }
}
