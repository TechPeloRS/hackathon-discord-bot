<?php

namespace App\Actions\Teams\Spawn;

use App\Models\Team\Team;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;
use Discord\Parts\Part;
use function React\Async\await;

class SpawnTeamAction
{
    private array $newChannels = [
        [
            'name' => 'geral',
            'category' => Channel::TYPE_TEXT
        ],
//        [
//            'name' => 'links-uteis',
//            'category' => Channel::TYPE_TEXT
//        ],
        [
            'name' => 'Voz',
            'category' => Channel::TYPE_VOICE
        ],
    ];

    public function handle(Team $team)
    {
        /** @var Guild $guild */
        $guild = app('bot')
            ->discord()
            ->guilds
            ->get('id', $team->guild_id);

        $this->createRole($team, $guild);

        $payload = $this->buildBaseCategory($guild, $team);
        $category = await($guild->channels->save($payload));

        $this->createChannels($team, $guild, $category);
    }


    private function createChannels(Team $team, Guild $guild, Channel $channel): void
    {
        $team = $team->refresh();
        $categoryId = $channel->id;
        $mentorId = $guild->roles->find(fn(Role $role) => $role->name === 'Pessoa Mentora')->id;
        $roleId = $team->role_id;
        $everyoneRole = $guild->roles->first()->id;

        $channelsToUpdate = [$channel->id];
        foreach ($this->newChannels as $newChannel) {

            $channel = $guild->channels->create([
                'name' => $newChannel['name'],
                'parent_id' => $categoryId,
                'type' => $newChannel['category'],
                'permission_overwrites' => [
                    [
                        "id" => $everyoneRole,
                        "type" => 0,
                        "allow" => '0',
                        "deny" => '1049600'
                    ],
                    [
                        "id" => $mentorId,
                        "type" => 0,
                        "allow" => '1049600',
                        "deny" => '0'
                    ],
                    [
                        "id" => $roleId,
                        "type" => 0,
                        "allow" => '1049600',
                        "deny" => '0'
                    ]
                ],
            ]);

            /** @var Channel $fuckingChannel */
            $fuckingChannel = await($guild->channels->save($channel));
            $channelsToUpdate[] = $fuckingChannel->id;
        }

        $team->update(['channels_ids' => $channelsToUpdate]);
    }

    private function defaultPermissions(Guild $guild): array
    {
        return [
            "id" => $guild->roles->first()->id,
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
                $this->defaultPermissions($guild)
            ],
        ]);
    }

    private function createRole(Team $team, Guild $guild): Role
    {
        $roleDTO = $guild->roles->create([
            'name' => 'Time ' . $team->getKey(),
            'color' => 0x00FF00,
            'hoist' => false,
            'mentionable' => true,
        ]);

        $role = await($guild->roles->save($roleDTO));
        $team->updateRole($role->id);

        return $role;
    }
}
