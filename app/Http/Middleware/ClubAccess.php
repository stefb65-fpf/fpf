<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClubAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // on vérifie que l'utilisateur a bien un accès Club
        $menu = $request->session()->get('menu');
        if (!$menu || !$menu['club']) {
            return redirect('/');
        }
        return $next($request);
    }
}
