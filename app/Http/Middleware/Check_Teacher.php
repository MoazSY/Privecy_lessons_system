<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Check_Teacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        // $teacher=$token->tokenable;
        // $teacher=Teacher::findOrFail($teacher->id);
        if (!$token || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['message' => 'Token has expired'], 401);
        }
        if (!Auth::guard('teacher')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // if($teacher->Activate_Account==false){
        //     return response()->json(['message' => 'teacher dont complete his account'], 401);
        // }
        return $next($request);
    }
}
