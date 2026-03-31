<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$usersWithoutPassword = User::whereNull('password')->count();
$usersWithNationalCode = User::whereNotNull('nationalcode')->count();
$usersWithoutPasswordWithNationalCode = User::whereNull('password')->whereNotNull('nationalcode')->count();

echo "Users without password: {$usersWithoutPassword}\n";
echo "Users with national code: {$usersWithNationalCode}\n";
echo "Users without password but with national code: {$usersWithoutPasswordWithNationalCode}\n";