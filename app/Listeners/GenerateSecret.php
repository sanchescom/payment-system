<?php

namespace App\Listeners;

use App\Entities\Secret;
use App\Events\CreateUser;

class GenerateSecret
{
	/**
	 * Create the event listener.
	 */
	public function __construct()
	{

	}


	/**
	 * Handle the event which create secret code and hashing it and save for user.
	 *
	 * @param  CreateUser $event
	 * @return Secret
	 */
	public function handle(CreateUser $event)
	{
		$secret = Secret::create();

		$event->user->setSecret($secret->getHash());

		return $secret->getCode();
	}
}
