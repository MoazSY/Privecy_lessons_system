<?php
namespace App\Repositories;

use App\Models\Admin;
use App\Models\RefreshToken;
use App\Models\Students;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
class TokenRepositories implements TokenRepositoriesInterface{

    public function Add_expierd_token($plainTextToken)
    {
        [$id, $token] = explode('|', $plainTextToken);
        PersonalAccessToken::find($id)->update([
            'expires_at' => now()->addHours(24)
        ]);
    }
    public function Refresh_token($refresh_token)
    {
        $refreshToken = DB::table('refresh_tokens')
            ->where('refresh_token', $refresh_token)
            ->where('expires_at', '>', now())
            ->first();
        return $refreshToken;
    }
    public function get_refresh_token_user($refresh_token)
    {
        $refresh_token = RefreshToken::where('refresh_token', $refresh_token)->first();
        $user = $refresh_token->user_table;
        return $user;
    }
    public function Add_refresh_token($plainTextToken)
    {
        $refreshToken = Str::random(64);
        $token = PersonalAccessToken::findToken($plainTextToken);
        $user = $token->tokenable;
        if($user instanceof \App\Models\Students){
            $user = Students::findOrFail($user->id);
            $refresh_token = $user->refreshTokens()->create([
                'refresh_token' => $refreshToken,
                'expires_at' => now()->addDay(7),
            ]);
        }elseif($user instanceof \App\Models\Teacher){
            $user = Teacher::findOrFail($user->id);
            $refresh_token = $user->refreshTokens()->create([
                'refresh_token' => $refreshToken,
                'expires_at' => now()->addDay(7),
            ]);
        }
        else{
            $user = Admin::findOrFail($user->id);
            $refresh_token = $user->refreshTokens()->create([
                'refresh_token' => $refreshToken,
                'expires_at' => now()->addDay(7),
            ]);
        }
        // DB::table('refresh_tokens')->insert([
        //     'user_id' => $user->id,
        //     'refresh_token' => $refreshToken,
        //     'expires_at' => now()->addDay(7),
        // ]);

        return $refreshToken;
    }

}
