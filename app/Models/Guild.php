<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $teams_count
 */
class Guild extends Model
{
    protected $fillable = [
        'provider_id',
        'main_server',
        'invite_url',
        'teams_count',
    ];
}
