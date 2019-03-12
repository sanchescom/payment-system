<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $email = $request->header('Authorization');

        if (empty($email)) {
            throw new BadRequestHttpException('Missing user email');
        }

        $user = User::findByEmail($email);

        if ($user == null) {
            throw new AccessDeniedHttpException('Access denied');
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
