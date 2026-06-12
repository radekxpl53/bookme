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
    '#0d6efd', // blue
    '#198754', // green
    '#dc3545', // red
    '#fd7e14', // orange
    '#6f42c1', // purple
    '#d63384', // pink
    '#20c997', // teal
    '#0dcaf0', // cyan
];

$events = $appointments->map(function ($apt) use ($colors) {
    $color = $colors[$apt->employee_id % count($colors)];
    if ($apt->status === 'cancelled') {
        $color = '#6c757d'; // grey out cancelled
    }

    return [
        'id' => $apt->id,
        'title' => $apt->client->first_name . ' ' . $apt->client->surname . ' - ' . $apt->service->name,
        'start' => $apt->start_at->toIso8601String(),
        'end' => $apt->finish_at->toIso8601String(),
        'backgroundColor' => $color,
        'borderColor' => $color,
        'extendedProps' => [
            'client_name' => $apt->client->first_name . ' ' . $apt->client->surname,
            'client_phone' => $apt->client->phone ?? 'Brak numeru',
            'service_name' => $apt->service->name,
            'employee_name' => $apt->employee->name,
            'status' => $apt->status,
            'price' => $apt->total_price,
        ]
    ];
});

echo json_encode($events, JSON_PRETTY_PRINT);
