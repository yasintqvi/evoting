<?php

// Test permissions for current user
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Get kernel
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Enums\Permission;
use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

// Get current user
$user = User::find(4); // یا هر ID مناسب

if (! $user) {
    echo "No user logged in!\n";
    exit;
}

echo 'User ID: '.$user->id."\n";
echo 'User Name: '.$user->full_name."\n\n";

// Check permissions
echo "Checking permissions:\n";
echo 'CREATE_GROUP: '.($user->can(Permission::CREATE_GROUP->value) ? 'YES' : 'NO')."\n";
echo 'EDIT_GROUP: '.($user->can(Permission::EDIT_GROUP->value) ? 'YES' : 'NO')."\n";
echo 'UPDATE_GROUP: '.($user->can(Permission::UPDATE_GROUP->value) ? 'YES' : 'NO')."\n";
echo 'DELETE_GROUP: '.($user->can(Permission::DELETE_GROUP->value) ? 'YES' : 'NO')."\n\n";

// Get first group
$group = Group::first();

if ($group) {
    echo 'Group ID: '.$group->id."\n";
    echo 'Group Title: '.$group->title."\n";
    echo 'Group Owner ID: '.$group->owner_id."\n\n";

    echo "Group permissions:\n";
    echo 'Can edit: '.($user->can('edit', $group) ? 'YES' : 'NO')."\n";
    echo 'Can update: '.($user->can('update', $group) ? 'YES' : 'NO')."\n";
    echo 'Can delete: '.($user->can('delete', $group) ? 'YES' : 'NO')."\n";
} else {
    echo "No group found!\n";
}
