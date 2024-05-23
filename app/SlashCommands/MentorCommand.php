<?php

namespace App\SlashCommands;

use App\Actions\Mentors\Join\MentorJoinAction;
use App\Actions\Mentors\MentorCommandsEnum;
use App\Actions\Mentors\MentorDTO;
use App\Enums\TeamRoleEnum;
use App\Exceptions\CommandException;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\User\Member;
use Laracord\Commands\SlashCommand;
use function React\Async\await;

class MentorCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'mentores';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'The mentor-command slash command.';

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
        $command = MentorCommandsEnum::from($interaction->data->options->first()->name);
        $dto = MentorDTO::makeFromInteraction($interaction);

        try {
            $response = $command->getAction()->handle($dto);
        } catch (CommandException $e) {
            $response = $e->buildErrorMessage();
        }

        $interaction->respondWithMessage($response, true);
    }


    public function options(): array
    {

        return [
            [
                "type" => Option::SUB_COMMAND,
                "name" => "entrar",
                "description" => "Entrar como mentor.",
                "options" => [
                    [
                        "type" => Option::STRING,
                        "name" => "email",
                        "description" => "Seu endereço de email cadastrado como mentor.",
                        "required" => true
                    ]
                ]
            ],
            [
                "type" => Option::SUB_COMMAND,
                "name" => "pedir-ajuda",
                "description" => "Peça ajuda para nossos mentores de plantão!",
                "options" => [
                    [
                        "type" => Option::STRING,
                        "name" => "tipo-mentoria",
                        "description" => "Informe qual tipo de pessoa você precisa de mentoria.",
                        "required" => true,
                        "choices" => collect(TeamRoleEnum::cases())->map(fn(TeamRoleEnum $role) => [
                            'name' => $role->getDescription(),
                            'value' => $role->value
                        ])->toArray()
                    ],
                    [
                        "type" => Option::STRING,
                        "name" => "contexto",
                        "description" => "Descreva seu desafio atual.",
                        "required" => true
                    ],
                ]
            ]
        ];
    }
}
