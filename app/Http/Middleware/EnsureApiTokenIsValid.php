<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the "Authorization" header
        $token = $request->header('Authorization');

        // Validate the token (you can customize this logic)
        if (!$token || $token !== session('api_token')) {
            abort('401');
        }

        return $next($request);
    }
}
