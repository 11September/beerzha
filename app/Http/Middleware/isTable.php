<?php

namespace App\Http\Middleware;

use App\Beerga;
use Closure;

class isTable
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
        $table = Beerga::where('key', 'table')->first();
        dd($table);

        return $next($request);
    }
}
