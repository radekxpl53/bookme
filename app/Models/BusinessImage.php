<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessImage extends Model
{
    public $timestamps = false;

    protected $table = 'businesses_images';

    protected $fillable = [
        'business_id',
        'images_id',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'images_id');
    }
}
