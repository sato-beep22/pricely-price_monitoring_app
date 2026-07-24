<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->phoneVerified()) {
            return redirect()->route('profile.edit')->with('error', 'You must verify your phone number before subscribing to price alerts.');
        }

        return $next($request);
    }
}
