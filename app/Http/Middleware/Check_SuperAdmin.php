<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Check_SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $admin=$token->tokenable;
        $Admin=Admin::findOrFail($admin->id);
        if($Admin->SuperAdmin==false){
            return response()->json(['message' => 'Admin is not a super admin'], 401);
        }

        return $next($request);
    }
}
