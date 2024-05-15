<?php

namespace App\Console\Commands;

use App\Discord\Events\MessageCreate;
use App\Discord\Events\SlashCommands;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Illuminate\Console\Command;

class StartServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws IntentException
     */
    public function handle(
        MessageCreate $messageCreate,
        SlashCommands $slashCommands,
    ): int
    {
        $discord = new Discord([
            'token' => config('services.discord.token'),
            'intents' => Intents::getDefaultIntents(),
        ]);

        $discord->on('ready', function (Discord $discord) use ($messageCreate, $slashCommands){
            $discord->on(Event::MESSAGE_CREATE, fn ($message) => $messageCreate->handle($message, $discord));
            $slashCommands->setup($discord);
        });

        $discord->run();

        return self::SUCCESS;
    }
}
