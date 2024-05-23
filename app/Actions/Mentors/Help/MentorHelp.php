<?php

namespace App\Actions\Mentors\Help;

use App\Actions\Mentors\MentorCommandInterface;
use App\Actions\Mentors\MentorDTO;
use App\Bot;
use Discord\Builders\MessageBuilder;
use Laracord\Discord\Message;
use React\Promise\Promise;
use function React\Async\await;

class MentorHelp implements MentorCommandInterface
{
    public function handle(MentorDTO $dto): MessageBuilder
    {
        $this->sendHelpRequest($dto);

        return $this->respondWithMessage();
    }


    private function sendHelpRequest(MentorDTO $dto)
    {
        $server = app('bot')->discord();
        $guild = $server->guilds->get('id', config('bot.main_guild'));
        $channel = $guild
            ->channels
            ->find(fn($channel) => str_contains($channel->name, 'pedidos-de-ajuda'));

        $messageBuilder = Message::make(null)
            ->title('Pedido de Mentoria')
            ->username('danielhe4rt')
            ->content("O usuário <@{$dto->member->id}> pediu ajuda de um mentor!")
            ->field('server','https://discord.gg/basementdevs')
            ->field('contexto',  $dto->content)
            ->info()
            ->timestamp();

        /** @var \Discord\Parts\Channel\Message $message */
        $message = await($channel->sendMessage($messageBuilder->build()));

        await($message->react('👍'));
    }

    public function respondWithMessage(): MessageBuilder
    {
        return Message::make(app('bot'))
            ->content('Você requisitou a ajuda de um mentor! Em breve alguém te contatará via chat ou irá entrar na sua sala de voz.')
            ->build();
    }
}
