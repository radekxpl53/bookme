<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
            'name',
            'category',
            'address',
            'description',
            'lat',
            'lon',
            'owner_id',
            'is_approved',
        ];

    protected function casts(): array
    {
        return [
            'lon' => 'decimal:7',
            'lat' => 'decimal:7',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Business $business) {
            $employeeIds = $business->employees()->pluck('id');

            \App\Models\Appointment::whereIn('employee_id', $employeeIds)->delete();
            \App\Models\WorkingHour::whereIn('employee_id', $employeeIds)->delete();
            \App\Models\EmployeeReview::whereIn('employee_id', $employeeIds)->delete();
            \Illuminate\Support\Facades\DB::table('employee_service')->whereIn('employee_id', $employeeIds)->delete();
            \App\Models\EmployeePortfolio::whereIn('employee_id', $employeeIds)->delete();
            \Illuminate\Support\Facades\DB::table('employee_reviews_images')->whereIn('employee_id', $employeeIds)->delete();

            $business->employees()->delete();
            $business->services()->delete();
            $business->reviews()->delete();
            $business->blacklist()->delete();
            $business->photos()->delete();

            \Illuminate\Support\Facades\DB::table('businesses_images')->where('business_id', $business->id)->delete();
            \Illuminate\Support\Facades\DB::table('businesse_reviews_images')->where('business_id', $business->id)->delete();
        });
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
