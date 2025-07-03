<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->status === 'banned') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been banned. Please contact support.'
                ]);
            }
            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is inactive. Please contact support.'
                ]);
            }
        }
        return $next($request);
    }
} 