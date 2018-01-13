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
        $MRT=MRTMaster::where('MCTNo',$request->id)->whereNull('Status')->whereNull('IsRollBack')
        ->orWhere('Status','0')->where('MCTNo',$request->id)->whereNull('IsRollBack')
        ->orWhere('IsRollBack','1')->where('MCTNo',$request->id)->whereNull('Status')
        ->orWhere('Status','0')->where('IsRollBack','1')->where('MCTNo',$request->id)->value('MRTNo');
        if ($MRT==null)
        {
          return $next($request);
        }else
        {
          return response()->json(['redirect'=>route('WelcomePage')]);
        }
    }
}
