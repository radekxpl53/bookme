<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$businessId = 7;
$appointments = \App\Models\Appointment::with('employee')->whereHas('employee', function($q) use ($businessId) {
    $q->where('business_id', $businessId);
})->get();

foreach ($appointments as $apt) {
    echo "ID: {$apt->id}, Employee: {$apt->employee_id}, Start: {$apt->start_at}, End: {$apt->finish_at}\n";
}
