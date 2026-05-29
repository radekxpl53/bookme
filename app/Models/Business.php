<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'lon',
        'lat',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'lon' => 'decimal:7',
            'lat' => 'decimal:7',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    public function blacklist(): HasMany
    {
        return $this->hasMany(BusinessBlacklist::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(BusinessPhoto::class);
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'businesses_images', 'business_id', 'images_id');
    }

    public function reviewImages(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'businesse_reviews_images', 'business_id', 'images_id');
    }
}
