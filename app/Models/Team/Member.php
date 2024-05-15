<?php

namespace App\Models\Team;

use App\Enums\TeamRoleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    protected $fillable = [
        'team_id',
        'discord_id',
        'role_type',
        'github_username',
    ];

    protected $casts = [
        'role_type' => TeamRoleEnum::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
