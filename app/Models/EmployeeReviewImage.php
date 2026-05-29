<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeReviewImage extends Model
{
    public $timestamps = false;

    protected $table = 'employee_reviews_images';

    protected $fillable = [
        'employee_id',
        'images_id',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'images_id');
    }
}
