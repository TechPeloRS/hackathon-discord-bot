<?php

namespace App\Models\Mentor;

use App\Actions\Mentors\MentorDTO;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    protected $fillable = [
        'provider_id',
        'email',
        'accepted_at'
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'timestamp',
        ];
    }

    public function acceptInvite(MentorDTO $dto): void
    {
        $this->update([
            'accepted_at' => now(),
            'provider_id' => $dto->member->user->id,
        ]);
    }
}
