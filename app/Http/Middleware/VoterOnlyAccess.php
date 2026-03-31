<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VoterOnlyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isVoterOnly()) {
            $allowedRoutes = [
                'app.index',
                'my-elections.index',
                'voting.create',
                'voting.store',
                'my-surveys.index',
                'surveys.answer',
                'surveys.answer.store',
                'logout',
            ];

            $routeName = $request->route()?->getName();

            if (!in_array($routeName, $allowedRoutes)) {
                return redirect()->route('app.index');
            }
        }

        return $next($request);
    }
}

