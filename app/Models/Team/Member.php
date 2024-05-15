<?php

namespace App\Models\Team;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    protected $fillable = [
        'team_id',
        'role_type',
        'name',
        'github_username',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
