<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsVendor
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
        if(auth()->user()->vendor()){
            return $next($request);
        } else if (session('logAsVendor')) {
            return $next($request);
        }

        return redirect('/')->with('error',"You don't have access.");
    }
}
