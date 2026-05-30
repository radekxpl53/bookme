<?php

namespace App\Http\Controllers;

use App\Models\Business;

class BusinessPublicController extends Controller
{
    public function show(Business $business)
    {
        $business->load([
            'services',
            'employees',
            'reviews.user',
        ]);

        $averageRating = $business->reviews->avg('rating');
        $reviewsCount  = $business->reviews->count();

        return view('business.show', compact(
            'business',
            'averageRating',
            'reviewsCount'
        ));
    }
}
