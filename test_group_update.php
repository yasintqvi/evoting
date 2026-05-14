<?php

// Test file to check group data
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Get kernel
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Group;
use Illuminate\Contracts\Console\Kernel;

// Get first group
$group = Group::first();

if ($group) {
    echo 'Group ID: '.$group->id."\n";
    echo 'Group Title: '.$group->title."\n";
    echo 'Group Type: '.$group->type->value."\n";
    echo 'Normal Stock Count: '.($group->normal_stock_count ?? 'null')."\n";
    echo 'Preferred Stock Count: '.($group->prefered_stock_count ?? 'null')."\n";
    echo 'Preferred Stock Weight: '.($group->prefered_stock_weight ?? 'null')."\n";

    echo "\nTesting update...\n";

    // Try to update
    $result = $group->update([
        'normal_stock_count' => 100,
        'prefered_stock_count' => 50,
        'prefered_stock_weight' => 2.5,
    ]);

    echo 'Update result: '.($result ? 'success' : 'failed')."\n";

    // Refresh and check
    $group->refresh();
    echo "After update:\n";
    echo 'Normal Stock Count: '.($group->normal_stock_count ?? 'null')."\n";
    echo 'Preferred Stock Count: '.($group->prefered_stock_count ?? 'null')."\n";
    echo 'Preferred Stock Weight: '.($group->prefered_stock_weight ?? 'null')."\n";

} else {
    echo "No group found!\n";
}
