<?php
namespace App\Repositories;
interface TokenRepositoriesInterface{
    public function Add_expierd_token($token);
    public function Refresh_token($token);
    public function Add_refresh_token($token);
    public function get_refresh_token_user($refresh_token);
    
}

