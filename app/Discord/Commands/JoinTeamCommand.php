<?php

namespace App\Discord\Commands;

use App\Enums\TeamRoleEnum;
use App\Models\Team\Member;
use App\Models\Team\Team;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;

class JoinTeamCommand implements CommandInterface
{

    public function handle(Discord $discord, Interaction $interaction): void
    {
        $teamKey = $interaction->data->options->pull('chave')->value;
        $selectedMemberRoleType = $interaction->data->options->pull('area')->value;
        $githubUsername = $interaction->data->options->pull('github')?->value;
        $memberDiscordId = $interaction->member->user->id;

        $memberIsAlreadyInATeam = Member::query()->where('discord_id', $memberDiscordId)->exists();

        if ($memberIsAlreadyInATeam) {
            $interaction->respondWithMessage(MessageBuilder::new()
                ->setContent('Você já está em um time!')
            );
            return;
        }

        $isAnEmail = filter_var(strtolower($teamKey), FILTER_VALIDATE_EMAIL);
        $team = $isAnEmail
            ? Team::query()->where('owner_email', $teamKey)->first()
            : Team::query()->where('code', $teamKey)->first();

        if (!$team) {
            $interaction->respondWithMessage(MessageBuilder::new()
                ->setContent('Time não encontrado! Contate seu líder e peça o código de acesso.')
            );
            return;
        }

        if ($team->hasMaxMembers()) {
            $interaction->respondWithMessage(MessageBuilder::new()
                ->setContent('Esse time já está cheio!')
            );
            return;
        }

        $team->members()->create([
            'discord_id' => $memberDiscordId,
            'role_type' => TeamRoleEnum::from($selectedMemberRoleType),
            'github_username' => $githubUsername
        ]);


        $interaction->respondWithMessage(MessageBuilder::new()
            ->setContent('Lider cadastrado!')
        );
    }
}
