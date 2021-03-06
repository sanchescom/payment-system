<?php

namespace App\Http\Controllers;

use App\Entities\Secret;
use App\Events\CreateUser;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    public function createNew(Request $request)
    {
        $this->validate(
            $request,
            [
                'name'     => 'required|max:255',
                'country'  => 'required|max:2',
                'city'     => 'required|max:200',
                'currency' => 'required|max:3',
                'email'    => 'required|max:255',
            ]
        );

        try {
            $user = User::create($request->all());

            /** @var Secret $secret */
            list($account, $secret) = \Event::dispatch(new CreateUser($user));
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Creating user error', $exception);
        }

        return response()->json(
            [
            [
                'data' => [
                    'user'    => $user->toArray(),
                    'secret'  => $secret,
                    'account' => $account,
                ],
            ],
            ],
            Response::HTTP_OK
        );
    }


    public function getAll()
    {
        try {
            $users = UserRepository::getUsers();
        } catch (\Exception $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Getting users error', $exception);
        }

        return response()->json([
            'data' => $users->toArray(),
        ], Response::HTTP_OK);
    }
}
