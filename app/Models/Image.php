<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Image extends Model
{
    protected $fillable = [
        'file_name',
        'original_file_name',
        'file_type',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'businesses_images', 'images_id', 'business_id');
    }

    public function businessReviewImages(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'businesse_reviews_images', 'images_id', 'business_id');
    }

    public function employeeReviewImages(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_reviews_images', 'images_id', 'employee_id');
    }
}
