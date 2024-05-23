<?php

namespace Database\Seeders;

use App\Enums\TeamNicheEnum;
use App\Models\Guild;
use App\Models\Team\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private int $teamsPerGuild;

    public function run(): void
    {
        // 3
        $this->setTeamsPerGuild();

        $teams = range(1, 300);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Team::truncate();
        foreach ($teams as $team) {
            Team::create([
                'owner_email' => sprintf('d+%s@d.com', $team),
                'niche_type' => TeamNicheEnum::Unknown,
                'members_count' => 0,
                'channels_ids' => [],
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function setTeamsPerGuild(): void
    {
        $this->teamsPerGuild = config('bot.teamsPerGuild');
    }
}
