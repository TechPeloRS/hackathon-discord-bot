<?php

namespace App\Models\Team;

use App\Enums\TeamNicheEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array $channels_ids
 */
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

    protected function casts(): array
    {
        return [
            'niche_type' => TeamNicheEnum::class,
            'channels_ids' => 'array',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'team_id');
    }


    public function hasMaxMembers(): bool
    {
        return $this->members_count >= 5;
    }
}
