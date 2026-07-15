<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'parashar.anchal@outlook.co')->first();
if (!$user) {
    echo "User not found by email.\n";
    $user = \App\Models\User::first();
}

if ($user) {
    echo "Current user details:\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Salon Name: " . $user->salon_name . "\n";
    echo "Slug: " . $user->slug . "\n";

    echo "Attempting to update salon name to 'Max Salon Test'...\n";
    $user->salon_name = 'Max Salon Test';
    $saved = $user->save();
    echo "Save result: " . ($saved ? "SUCCESS" : "FAILED") . "\n";

    $user->refresh();
    echo "After refresh Salon Name: " . $user->salon_name . "\n";
    echo "After refresh Slug: " . $user->slug . "\n";
} else {
    echo "No users in the database.\n";
}
