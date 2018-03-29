<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
	use ValidatesRequests;

	private static $user;


	/**
	 * @return User
	 */
	protected function getCurrentUser()
	{
		return self::$user;
	}


	public static function setUser($user)
	{
		self::$user = $user;
	}
}
