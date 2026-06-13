<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessReview;
use Illuminate\Support\Facades\Auth;

class BusinessPublicController extends Controller
{
    public function show(Business $business)
    {
        abort_if(!$business->is_approved, 404);

        $business->load([
            'services',
            'employees.reviews.user',
            'employees.reviewImages',
            'reviews.user',
            'reviewImages',
        ]);

        $averageRating = $business->reviews->avg('rating');
        $reviewsCount  = $business->reviews->count();

        $myReview = Auth::check()
            ? BusinessReview::where('business_id', $business->id)->where('user_id', Auth::id())->first()
            : null;

        return view('business.show', compact(
            'business',
            'averageRating',
            'reviewsCount',
            'myReview'
        ));
    }
}
