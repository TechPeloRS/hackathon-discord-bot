<?php

namespace App\Console\Commands;

use App\Models\Team\Member;
use App\Models\Team\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Stringable;

class LoadHackathonCommand extends Command
{

    protected $signature = 'load:hackathon {--participants=} {--mentors=}';

    protected $description = 'Load the hackathon data';

    public function handle(): int
    {
        $participantsSpreadsheetUrl = str($this->option('participants'));
        $mentorsSpreadsheetUrl = str($this->option('mentors'));

        if (!$participantsSpreadsheetUrl->isUrl()) {
            $this->error("Invalid participants spreadsheet URL");
            return self::FAILURE;
        }

        if (!$mentorsSpreadsheetUrl->isUrl()) {
            $this->error("Invalid mentors spreadsheet URL");
            return self::FAILURE;
        }

        $this->wipeTeams();
        $this->loadParticipants($participantsSpreadsheetUrl);

        $this->info("vai caraio");
        return self::SUCCESS;
    }

    private function loadParticipants(Stringable $participantsSpreadsheetUrl): void
    {

        $participantsList = str(file_get_contents($participantsSpreadsheetUrl))
            ->explode(PHP_EOL)
            ->map(fn(string $participantEmail) => str($participantEmail)->lower());

        $this->info(sprintf("Loading %s participants from $participantsSpreadsheetUrl", count($participantsList)));

        $participantsList
            ->each(function (Stringable $participantEmail) {
                Team::query()->create([
                    'owner_email' => $participantEmail->toString(),
                    'channels_ids' => []
                ]);
                $this->info("Participant $participantEmail loaded");
            });
    }

    private function wipeTeams(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Team::truncate();
        Member::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
