<?php

namespace App\Listeners;

use App\Events\CreateUser;
use App\Services\AccountProcessor;

class GenerateAccount
{
	public $account_processor;


	/**
	 * Create the event listener.
	 *
	 * @param AccountProcessor $account_processor
	 */
	public function __construct(AccountProcessor $account_processor)
	{
		$this->account_processor = $account_processor;
	}


	/**
	 * Handle the event which generate user account.
	 *
	 * @param  CreateUser $event
	 * @return string
	 * @throws \Exception
	 */
	public function handle(CreateUser $event)
	{
		$event->user->setAccount(
			$this->account_processor->generate($event->user->currency, $event->user->id)
		);

		return $event->user->getAccount();
	}
}
