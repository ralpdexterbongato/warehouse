<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class IsWarehouse
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
      if ((Auth::user()->Role==4))
      {
        return $next($request);
      }
        return redirect('/');
    }
}
