<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('personal_access_tokens')
        ->where('expires_at', '<', now())
            ->delete();

        DB::table('refresh_tokens')
        ->where('expires_at', '<', now())
            ->delete();

        $this->info('Expired tokens deleted successfully.');
    }
}
