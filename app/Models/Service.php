<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'price',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_service');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
