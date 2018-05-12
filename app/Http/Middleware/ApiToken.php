<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = User::where('token', '=', $request->header('x-auth-token'))->first();

        if($token && $request->header('x-auth-token')){
            return $next ($request);
        }else{
            return response()->json(['message' => 'Невідповідність токена або користувач не авторизований'], 403);
        }

//        if (($request->header('x-auth-token')) ===  Auth::user()->token){
//            return $next ($request);
//        }else{
//
//            Log::info('Token is: '. $request->header('x-auth-token'));
//            Log::info('User-agent is: '. $request->header('user-agent'));
//
//            return response()->json(['message' => 'Невідповідність токена або користувач не авторизований'], 403);
//        }
    }
}
