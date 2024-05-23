<?php

namespace Database\Seeders;

use App\Models\Guild;
use Illuminate\Database\Seeder;

class GuildsSeeder extends Seeder
{
    public function run(): void
    {
        Guild::truncate();
        foreach (config('bot.guilds') as $guild) {
            Guild::query()->create($guild);
        }
    }
}
