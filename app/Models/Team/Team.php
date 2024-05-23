<?php

namespace App\Models\Team;

use App\Actions\Teams\Join\JoinTeamDTO;
use App\Enums\TeamNicheEnum;
use App\Models\Guild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array $channels_ids
 */
class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'guild_id',
        'role_id',
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

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class, 'guild_id', 'provider_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'team_id');
    }

    public function hasMaxMembers(): bool
    {
        return $this->members_count >= 5;
    }


    public function addMember(JoinTeamDTO $dto): void
    {
        $this->members()->create([
            'discord_id' => $dto->member->id,
            'role_type' => $dto->selectedMemberRoleType,
            'github_username' => $dto->member
        ]);
    }

    public function updateRole(string $roleId): void
    {
        $this->update([
            'role_id' => $roleId
        ]);
    }

    public function findByOwnerEmail(string $code): ?Team
    {
        return $this->query()->where('owner_email', $code)->first();
    }
}
