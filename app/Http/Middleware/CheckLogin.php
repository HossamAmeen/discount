<?php

namespace App\Http\Middleware;
use App\Http\Controllers\APIResponseTrait;
use Closure,Auth;

class CheckLogin
{
    use APIResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $guard = 'api')
    {
        if (Auth::guard($guard)->check()  ) {
            if (Auth::guard($guard)->user()->status == "accept"  ) {
            return $next($request);
            }
            else
            {
                return $this->APIResponse(null, "this accout not accept from admin because :" . Auth::guard($guard)->user()->block_reason, 401);
            }
        } else {
            return $this->APIResponse(null, "token is expired", 401);
        }
    }
}
