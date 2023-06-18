<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;

class ApiAuthentication
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
        $token = $request->bearerToken();
        // $user = User::where('api_token', $token)->first();
        $user = UserToken::where('token', $token)->first();
        if ($user) {
            auth()->login($user->user);
            return $next($request);
        }
        return response([
            'message' => 'Unauthenticated',
            'status' => 403
        ]);
    }
}
