<?php

namespace App\Discord\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Part;

class TestCommand implements CommandInterface
{

    private array $newChannels = [
        [
            'name' => 'geral',
            'category' => Channel::TYPE_TEXT
        ],
        [
            'name' => 'links-uteis',
            'category' => Channel::TYPE_TEXT
        ],
        [
            'name' => 'Voz',
            'category' => Channel::TYPE_VOICE
        ],
    ];

    private array $channelIds = [];

    public function handle(Discord $discord, Interaction $interaction): void
    {
        $team = ['name' => 'Time 1'];
        $guild = $discord->guilds->first();

//        if (config('app.env') == 'local-daniel') {
//            $guild->channels
//                ->filter(fn(Channel $channel) => $channel->getRawAttributes()['name'] != 'general')
//                ->map(fn(Channel $channel) => $guild->channels->delete($channel));
//        }

        $guild
            ->channels
            ->save($this->buildBaseCategory($guild, $team))
            ->done(fn(Channel $channel) => $this->createChannels($guild, $channel));

        $interaction->respondWithMessage(MessageBuilder::new()
            ->setContent('Canais criados! ' . implode(', ', $this->channelIds))
        );
    }

    private function defaultPermissions(): array
    {
        return [
            "id" => config('discord.roles.everyone'),
            "type" => 0,
            "allow" => "0",
            "deny" => "1049600"
        ];

    }

    private function buildBaseCategory(Guild $guild, array $team): Part
    {
        return $guild->channels->create([
            'name' => 'Time ' . $team['name'],
            'type' => Channel::TYPE_CATEGORY,
            'permission_overwrites' => [
                $this->defaultPermissions()
            ],
        ]);
    }

    private function createChannels(?Guild $guild, Channel $channel): void
    {
        $categoryId = $channel->getRawAttributes()['id'];
        $this->channelIds[] = $categoryId;

        foreach ($this->newChannels as $newChannel) {
            $channel = $guild->channels->create([
                'name' => $newChannel['name'] . ' ' . $channel->id,
                'parent_id' => $categoryId,
                'type' => $newChannel['category'],
            ]);

            $guild->channels->save($channel)
                ->done(function (Channel $channel) {
                    $channelId = $channel->getRawAttributes()['id'];
                });
        }
    }
}
