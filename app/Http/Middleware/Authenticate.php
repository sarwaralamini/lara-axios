<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Get the token from the "Authorization" header
        if (!Auth::check()) {
            // Store the full URL in the session
            session(['current_url' => $request->fullUrl()]);

            return redirect()->route('home');
        }

        session()->forget('current_url');

        return $next($request);
    }
}
