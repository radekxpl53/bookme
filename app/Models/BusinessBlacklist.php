<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessBlacklist extends Model
{
    const UPDATED_AT = null;

    protected $table = 'business_blacklist';

    protected $fillable = [
        'business_id',
        'user_id',
        'reason',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
