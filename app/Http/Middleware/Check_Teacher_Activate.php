<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Check_Teacher_Activate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $teacher=$token->tokenable;
        $teacher=Teacher::findOrFail($teacher->id);
        if($teacher->Activate_Account==false){
            return response()->json(['message' => 'teacher dont complete his account'], 401);
        }

        return $next($request);
    }
}
