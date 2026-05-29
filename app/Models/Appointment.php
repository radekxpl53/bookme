<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'employee_id',
        'service_id',
        'start_at',
        'finish_at',
        'status',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'finish_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
