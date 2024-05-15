<?php

namespace App\Models\Team;

use App\Discord\Commands\Teams\Join\JoinTeamDTO;
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


    public function addMember(JoinTeamDTO $dto): void
    {
        $this->members()->create([
            'discord_id' => $dto->member->id,
            'role_type' => $dto->selectedMemberRoleType,
            'github_username' => $dto->member
        ]);
    }

    public function findByCode(string $code): ?Team
    {
        // TODO: code or email? current using e-mail.
        $isAnEmail = filter_var(strtolower($code), FILTER_VALIDATE_EMAIL);

        return $isAnEmail
            ? Team::query()->where('owner_email', $code)->first()
            : Team::query()->where('code', $code)->first();
    }
}
