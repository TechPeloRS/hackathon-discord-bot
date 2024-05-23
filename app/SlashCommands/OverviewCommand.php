<?php

namespace App\SlashCommands;

use App\Models\Mentor\Mentor;
use App\Models\Team\Member;
use App\Models\Team\Team;
use Laracord\Commands\SlashCommand;

class OverviewCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'estatisticas';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'The overview-command slash command.';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The permissions required to use the command.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Indiciates whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = false;

    /**
     * Indicates whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the slash command.
     *
     * @param \Discord\Parts\Interactions\Interaction $interaction
     * @return void
     */
    public function handle($interaction)
    {
        $teamsWithOneOrMoreMembers = Team::query()
            ->whereHas('members')
            ->count();

        $totalMembers = Member::query()
            ->count();

        $mentorsIds = Mentor::query()
            ->whereNotNull('provider_id')
            ->pluck('provider_id')
            ->toArray();


        $onlineMentors = $interaction
            ->guild
            ->members
            ->filter(function (\Discord\Parts\User\Member $member) use ($mentorsIds) {
                return in_array($member->user->id, $mentorsIds)
                    && $member->status === 'online';
            })->count();

        $interaction->respondWithMessage(
            $this->message()
                ->title('Estatísticas Gerais')
                ->fields([
                    'Times Ativos' => $teamsWithOneOrMoreMembers,
                    'Participantes Ativos' => $totalMembers,
                    'Mentores Online' => $onlineMentors
                ])
                ->timestamp()
                ->build()
            , true
        );
    }
}
