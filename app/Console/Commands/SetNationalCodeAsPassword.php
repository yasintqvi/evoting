<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetNationalCodeAsPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:set-nationalcode-password {--dry-run : Run without actually updating users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set national code as password for users who don\'t have a password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $users = User::whereNull('password')
            ->whereNotNull('nationalcode')
            ->where('nationalcode', '!=', '');

        $count = $users->count();

        if ($count === 0) {
            $this->info('No users found that need password updates.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} users without passwords that have national codes.");

        if ($dryRun) {
            $this->info('Dry run mode - no changes will be made.');
            $users->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->line("Would update user: {$user->fullName} (ID: {$user->id}, National Code: {$user->nationalcode})");
                }
            });
        } else {
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            $users->chunk(100, function ($users) use ($progressBar) {
                foreach ($users as $user) {
                    $user->password = Hash::make($user->nationalcode);
                    $user->save();
                    $progressBar->advance();
                }
            });

            $progressBar->finish();
            $this->newLine();
            $this->info('Successfully updated all users.');
        }

        return Command::SUCCESS;
    }
}