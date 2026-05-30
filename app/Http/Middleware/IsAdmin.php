<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Dodaj import naszego modelu

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Brak dostępu - ta strona jest tylko dla administratorów.');
    }
}
