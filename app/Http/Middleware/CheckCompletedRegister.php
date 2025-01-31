<?php
/**
 * a middleware that check user complete register
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompletedRegister
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(Auth::check() && ! Auth::user()->completedRegister()){
            abort(403, 'Your register process not completed.');
        }
        return $next($request);
    }
}
