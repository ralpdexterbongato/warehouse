<?php

namespace App\Http\Middleware;

use Closure;
use App\MRTMaster;
class AlreadyHaveMRT
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
        $MRT=MRTMaster::where('MCTNo',$request->id)->value('MRTNo');
        if ($MRT==null)
        {
          return $next($request);
        }else
        {
          return redirect('/');
        }
    }
}