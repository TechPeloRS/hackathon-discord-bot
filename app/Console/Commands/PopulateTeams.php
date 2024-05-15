<?php

namespace App\Console\Commands;

use App\Enums\TeamNicheEnum;
use App\Models\Team\Team;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class PopulateTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teams:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emails = [
            'test1@example.com',
            'test2@example.com',
            'daniel@daniel.com'
        ];

        Team::truncate();

        foreach ($emails as $email) {
            Team::query()->create([
                'owner_email' => $email,
                'code' => Uuid::uuid4(),
                'niche_type' => TeamNicheEnum::Unknown,
                'channels_ids' => []
            ]);
        }

        return self::SUCCESS;
    }
}
