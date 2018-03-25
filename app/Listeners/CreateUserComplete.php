<?php

namespace App\Listeners;

use App\Events\CreateUser;

class CreateUserComplete
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Saving all changes from previous listeners and also can send some emails
     *
     * @param  CreateUser  $event
     * @return void
     */
    public function handle(CreateUser $event)
    {
        $event->user->save();
    }
}
