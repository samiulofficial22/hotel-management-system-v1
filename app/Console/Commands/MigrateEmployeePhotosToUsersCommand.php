<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class MigrateEmployeePhotosToUsersCommand extends Command
{
    protected $signature = 'app:migrate-employee-photos-to-users
                            {--dry-run : Show what would be updated without writing}';

    protected $description = 'Copy employees.photo path to users.profile_pic where user has no profile photo (one-way, does not delete old files)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn(__('Dry run – no changes will be written.'));
        }

        $employees = Employee::query()
            ->whereNotNull('user_id')
            ->whereNotNull('photo')
            ->with('user')
            ->get();

        $updated = 0;
        foreach ($employees as $employee) {
            $user = $employee->user;
            if (! $user) {
                continue;
            }
            if (! empty($user->profile_pic)) {
                continue;
            }
            if ($dryRun) {
                $this->line(__('Would set user :id profile_pic from employee :emp.', [
                    'id' => $user->id,
                    'emp' => $employee->id,
                ]));
                $updated++;
                continue;
            }
            $user->update(['profile_pic' => $employee->photo]);
            $updated++;
        }

        $this->info($dryRun
            ? __('Would update :count user(s).', ['count' => $updated])
            : __('Updated :count user(s) with employee photo path.', ['count' => $updated]));

        return self::SUCCESS;
    }
}
