<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPhoto extends Model
{
    protected $fillable = [
        'business_id',
        'path',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
