<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Repositories\AuthRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userRepository = new AuthRepository(new User());
        $selfData = $userRepository->self($request);
        $data = (array)$selfData->getData();
        if ($data && array_key_exists('status', $data)) {
            $request->request->add(['user_id' => $data['data']->id]);
            return $next($request);
        } else {
            return $selfData;
        }
    }
}
