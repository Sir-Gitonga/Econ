<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class AssignRolesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role_id to users based on their role column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('role_id')->get();

        if ($users->isEmpty()) {
            $this->info('All users already have role_id assigned.');
            return 0;
        }

        $count = 0;

        foreach ($users as $user) {
            // Map old role string to new role relationship
            $roleString = $user->role ?? 'user';

            // Get the role for this user's company
            $role = Role::where('company_id', $user->company_id)
                ->where('name', $roleString)
                ->first();

            if ($role) {
                $user->update(['role_id' => $role->id]);
                $count++;
                $this->line("✓ Assigned {$roleString} role to {$user->name}");
            } else {
                $this->warn("✗ Role '{$roleString}' not found for company {$user->company_id}");
            }
        }

        $this->info("\nSuccessfully assigned roles to {$count} users.");

        return 0;
    }
}
