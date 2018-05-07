<?php

namespace App\Http\Middleware;

use App\Beerga;
use Closure;

class isCode
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
        $code = Beerga::where('key', 'code')->first();

        if(intValue($code->value) == intValue($request->code)){
            return $next($request);
        }else{
            return response('Invalid');
        }
    }
}
