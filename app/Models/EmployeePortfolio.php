<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePortfolio extends Model
{
    protected $table = 'employee_portfolio';

    protected $fillable = [
        'employee_id',
        'path',
        'description',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
