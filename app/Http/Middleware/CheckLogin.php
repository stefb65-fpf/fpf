<?php

namespace App\Http\Middleware;

use App\Models\Personne;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        session()->put('previous_url', $request->getUri());
//        if ($_SERVER['REMOTE_ADDR'] == '92.137.109.86') {
//            dd($request->getUri());
//        }
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect('/login');
        }
        return $next($request);
    }
}
