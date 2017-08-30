<?php

namespace App\Http\Middleware;

use Closure;
use App\RRMaster;
class IfAlreadyHaveRR
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
        $RRCheck=RRMaster::where('RVNo', $request->id)->take(1)->value('RRNo');
        if ($RRCheck!=null)
        {
          return redirect('/');
        }else
        {
          return $next($request);
        }
    }
}
