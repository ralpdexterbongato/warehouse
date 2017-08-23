<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class IsGM
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
      if (Auth::user()->Role==2)
      {
        return $next($request);
      }else
      {
        return redirect('/');
      }
    }
}
