<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class IsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && ($user->isOwner() || $user->is_admin)) {
            return $next($request);
        }

        abort(403, 'Brak dostępu - musisz założyć profil swojego salonu, aby tu wejść.');
    }
}
