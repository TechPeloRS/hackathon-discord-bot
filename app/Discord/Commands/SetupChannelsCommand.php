<?php

namespace App\Discord\Commands;

use App\Models\Team\Team;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Part;

class SetupChannelsCommand implements CommandInterface
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


        $guild = $discord->guilds->first();
        if (config('app.env') == 'local-daniel') {
            $guild->channels
                ->filter(fn(Channel $channel) => $channel->getRawAttributes()['name'] != 'general')
                ->map(fn(Channel $channel) => $guild->channels->delete($channel));
        }

        /** @var Team $team */
        foreach (Team::all() as $team) {
            $guild
                ->channels
                ->save($this->buildBaseCategory($guild, $team))
                ->done(fn(Channel $channel) => $this->createChannels($team, $guild, $channel));

        }

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

    private function buildBaseCategory(Guild $guild, Team $team): Part
    {
        return $guild->channels->create([
            'name' => 'Time ' . $team->getKey(),
            'type' => Channel::TYPE_CATEGORY,
            'permission_overwrites' => [
                $this->defaultPermissions()
            ],
        ]);
    }

    private function createChannels(Team $team, Guild $guild, Channel $channel): void
    {
        $team->update(['channels_ids' => [$channel->id]]);
        $team = $team->refresh();

        $categoryId = $channel->getRawAttributes()['id'];

        foreach ($this->newChannels as $newChannel) {
            $channel = $guild->channels->create([
                'name' => $newChannel['name'] . ' ' . $channel->id,
                'parent_id' => $categoryId,
                'type' => $newChannel['category'],
            ]);

            $guild->channels->save($channel)
                ->done(function (Channel $channel) use ($team) {
                    $channelId = $channel->getRawAttributes()['id'];
                    $team = $team->refresh();
                    $team->update([
                        'channels_ids' => [...$team->channels_ids, $channelId]
                    ]);
                });
        }
    }
}
