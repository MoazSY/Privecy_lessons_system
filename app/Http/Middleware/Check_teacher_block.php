<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Check_teacher_block
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
        if($teacher->isBlocked()){
            return response()->json(['message' => 'teacher block until','block'=>$teacher->blocked_until], 401);
        }

        return $next($request);
    }
}
