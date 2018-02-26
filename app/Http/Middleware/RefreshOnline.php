<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Auth;
use Carbon\Carbon;
class RefreshOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check())
        {
          User::where('id',Auth::user()->id)->update(['LastOnline'=>Carbon::now()]);
        }
        return $next($request);
    }
}
