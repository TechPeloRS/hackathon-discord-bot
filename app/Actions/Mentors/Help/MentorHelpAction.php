<?php

namespace App\Actions\Mentors\Help;

use App\Actions\Mentors\MentorCommandInterface;
use App\Actions\Mentors\MentorDTO;
use App\Bot;
use App\Enums\TeamRoleEnum;
use App\Models\Team\Member;
use Discord\Builders\MessageBuilder;
use Laracord\Discord\Message;
use React\Promise\Promise;
use function React\Async\await;

class MentorHelpAction implements MentorCommandInterface
{
    public function handle(MentorDTO $dto): MessageBuilder
    {
        $this->sendHelpRequest($dto);

        return $this->respondWithMessage();
    }


    private function sendHelpRequest(MentorDTO $dto)
    {
        $server = app('bot')->discord();
        $guild = $server
            ->guilds
            ->get('id', config('bot.main_guild'));

        $channel = $guild
            ->channels
            ->find(fn($channel) => str_contains($channel->name, 'pedidos-de-ajuda'));

        $member = Member::where('discord_id', $dto->member->id)->first();
        if (!$member) {
            throw MentorHelpException::notRegistered();
        }

        $guildInviteLink = $member->team->guild->invite_url;

        $mentorType = $dto->args->pull('tipo-mentoria')->value;
        if ($mentorType === '')

        $mentorType = TeamRoleEnum::from($mentorType);



        $mentorTag = sprintf('<@&%s>', $mentorType->getDiscordId());

        $messageBuilder = Message::make(null)
            ->title('Pedido de Mentoria')
            ->content('**Contexto**: ' . $dto->args->pull('contexto')->value)
            ->field('Link pro servidor', $guildInviteLink)
            ->field('Mentor Requisitado', $mentorTag)
            ->info()
            ->timestamp();

        /** @var \Discord\Parts\Channel\Message $message */
        $message = await($channel->sendMessage($messageBuilder->build()));

        // emotion eyes
        await($message->react('✅'));
        // emotion check
        await($message->react('👀'));
    }

    public function respondWithMessage(): MessageBuilder
    {
        return Message::make(app('bot'))
            ->content('Você requisitou a ajuda de um mentor! Em breve alguém te contatará via chat ou irá entrar na sua sala de voz.')
            ->build();
    }
}
