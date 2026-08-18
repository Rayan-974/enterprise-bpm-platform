<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$finHead = App\Models\User::where('email', 'finance.head@enterprise.com')->first();
if ($finHead) {
    $updated = App\Models\Task::whereNull('assignee_id')
        ->where('status', 'pending')
        ->update(['assignee_id' => $finHead->id]);
    echo "Updated {$updated} pending unassigned task(s) to Finance Head (ID: {$finHead->id}).\n";
} else {
    echo "Finance head user not found.\n";
}
