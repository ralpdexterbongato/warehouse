<?php

namespace App\Http\Middleware;

use Closure;
use App\POMaster;
class IfAlreadyHavePO
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
        $pocheck=POMaster::where('RVNo',$request->id)->value('PONo');
        if ($pocheck!=null)
        {
          return redirect('/');
        }else
        {
          return $next($request);
        }
    }
}
