<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserPasswordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-passwords {--password=password : Plain password to set for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rehash all user passwords (use after DB reset or when "Bcrypt algorithm" login error occurs)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plain = $this->option('password');
        $count = User::query()->count();
        if ($count === 0) {
            $this->warn('No users found.');
            return self::SUCCESS;
        }

        $this->info("Updating password for {$count} user(s)…");
        User::query()->get()->each(function (User $user) use ($plain): void {
            $user->password = $plain;
            $user->save();
        });

        $this->info('Done. Users can now log in with the password you set.');
        return self::SUCCESS;
    }
}
