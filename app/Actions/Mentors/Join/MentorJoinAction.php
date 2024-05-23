<?php

namespace App\Actions\Mentors\Join;

use App\Actions\Mentors\MentorCommandInterface;
use App\Actions\Mentors\MentorDTO;
use App\Models\Mentor\Mentor;
use Discord\Builders\MessageBuilder;
use Laracord\Discord\Message;
use function React\Async\await;

class MentorJoinAction implements MentorCommandInterface
{
    public function handle(MentorDTO $dto): MessageBuilder
    {
        $mentor = Mentor::query()
            ->where('email', $dto->content)
            ->orWhere('provider_id', $dto->member->id)
            ->first();

        if (!$mentor) {
            throw MentorJoinException::notFound();
        }

        if ($mentor->accepted_at) {
            throw MentorJoinException::alreadyAccepted();
        }

        $mentor->acceptInvite($dto);
        $this->addRole($dto);

        return $this->respondWithMessage();
    }

    private function addRole(MentorDTO $dto): void
    {
        $mentorRole = $dto->guild->roles->find(
            fn($role) => $role->name === 'Pessoa Mentora'
        );

        await($dto->member->addRole($mentorRole));
    }

    private function respondWithMessage(): MessageBuilder
    {
        return Message::make(app('bot'))
            ->content('You have requested help from a mentor!')
            ->build();
    }
}
