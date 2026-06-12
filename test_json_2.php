<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$start = \Carbon\Carbon::parse('2026-06-08T00:00:00+02:00')->toDateTimeString();
$end = \Carbon\Carbon::parse('2026-06-15T00:00:00+02:00')->toDateTimeString();

$query = App\Models\Appointment::with(['client', 'service', 'employee'])
    ->whereHas('employee', function ($q) {
        $q->where('business_id', 7);
    })
    ->where('start_at', '>=', $start)
    ->where('start_at', '<', $end);

$appointments = $query->get();

$colors = [
    '#0d6efd',
];

$events = $appointments->map(function ($apt) use ($colors) {
    return [
        'id' => $apt->id,
        'title' => 'Test',
        'start' => $apt->start_at->format('Y-m-d\TH:i:s'),
        'end' => $apt->finish_at->format('Y-m-d\TH:i:s'),
    ];
});

echo json_encode($events, JSON_PRETTY_PRINT);
