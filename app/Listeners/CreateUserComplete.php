<?php

namespace App\Listeners;

use App\Events\CreateUser;
use App\Services\CurrencyCollector;

class CreateUserComplete
{
    protected $collector;


    public function __construct(CurrencyCollector $collector)
    {
        $this->collector = $collector;
    }


    /**
     * Saving all changes from previous listeners and also can send some emails
     *
     * @param  CreateUser $event
     * @return void
     */
    public function handle(CreateUser $event)
    {
        try {
            $this->collector->create($event->user->currency);
        } catch (\Exception $exception) {
            //TODO Send information to sentry
        }

        $event->user->save();
    }
}
