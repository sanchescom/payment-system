<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
	public function create(Request $request)
	{
		$this->validate($request, [
			'name'    => 'required|max:255',
			'country' => 'required|max:2',
			'city'    => 'required|max:200',
            'currency'=> 'required|max:3',
            'email'   => 'required|max:255',
		]);

		try
		{
			$secret = Secret::create();
			$user   = User::createWithSecret($request->all(), $secret);
		}
		catch (\Exception $exception)
		{
			throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Creating user error', $exception);
		}

		return response()->json([
			'user'   => $user->toArray(),
			'secret' => $secret->getCode(),
		], Response::HTTP_OK);
	}
}