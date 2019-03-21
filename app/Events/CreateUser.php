<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Class CreateUser.
 */
class CreateUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
