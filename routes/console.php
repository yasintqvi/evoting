<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('users:set-nationalcode-password', function () {
    $users = \App\Models\User::whereNull('password')
        ->whereNotNull('nationalcode')
        ->where('nationalcode', '!=', '');

    $count = $users->count();

    if ($count === 0) {
        $this->info('No users found that need password updates.');
        return;
    }

    $this->info("Found {$count} users without passwords that have national codes.");

    $progressBar = $this->output->createProgressBar($count);
    $progressBar->start();

    $users->chunk(100, function ($users) use ($progressBar) {
        foreach ($users as $user) {
            $user->password = bcrypt($user->nationalcode);
            $user->save();
            $progressBar->advance();
        }
    });

    $progressBar->finish();
    $this->newLine();
    $this->info('Successfully updated all users.');
})->purpose('Set national code as password for users who don\'t have a password');
