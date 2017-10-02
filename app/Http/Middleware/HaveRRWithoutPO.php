<?php

namespace App\Http\Middleware;

use Closure;
use App\RRMaster;
use App\POMaster;
class HaveRRWithoutPO
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
        $checkRR=RRMaster::where('RVNo', $request->id)->take(1)->value('RRNo');
        $checkPO=POMaster::where('RVNo', $request->id)->take(1)->value('PONo');
        if ($checkRR!=null&&$checkPO==null)
        {
          return redirect('/');
        }else
        {
          return $next($request);
        }
    }
}
