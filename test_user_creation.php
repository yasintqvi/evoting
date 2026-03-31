<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Create a test user without password but with national code
try {
    $user = User::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '09123456789',
        'nationalcode' => '1234567890',
        'is_active' => true,
    ]);

    echo "Created user: {$user->fullName}\n";
    echo "National code: {$user->nationalcode}\n";
    echo "Password: " . ($user->password ? 'Set' : 'Not set') . "\n";

    // Verify password is bcrypt of nationalcode
    if (password_verify($user->nationalcode, $user->password)) {
        echo "Password correctly set as bcrypt of national code!\n";
    } else {
        echo "Password verification failed!\n";
    }

    // Clean up
    $user->delete();
    echo "Test user deleted.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}