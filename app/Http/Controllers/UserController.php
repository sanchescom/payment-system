<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
	public function create(Request $request)
	{
		$this->validate($request, [
			'name'    => 'required|max:255',
			'country' => 'required|max:2',
			'city'    => 'required|max:200',
			'currency'=> 'required|max:3',
		]);

		try
		{
			$secret = str_random(3);

			$user = new User();
			$user->create($request->all())
                ->generatePurse($secret)
                ->save();
		}
		catch (\Exception $exception)
		{
			throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Creating user error', $exception);
		}

		return response()->json([
			'user'    => $user->toArray(),
			'secret'  => $secret,
		], Response::HTTP_OK);
	}
}