<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\EmployeeReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessReviewController extends Controller
{
    /**
     * Zabezpieczenie przed dostępem osób trzecich.
     */
    private function authorizeOwner(Business $business)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień. Ten lokal należy do kogoś innego.');
        }
    }

    public function index(Business $business)
    {
        $this->authorizeOwner($business);

        // Pobieramy opinie o biznesie wraz z autorem
        $businessReviews = BusinessReview::with('user')
            ->where('business_id', $business->id)
            ->latest('created_at')
            ->get();

        // Pobieramy opinie o pracownikach z tego biznesu
        $employeeReviews = EmployeeReview::with(['user', 'employee'])
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })
            ->latest('created_at')
            ->get();

        // Wyliczamy proste statystyki
        $avgBusinessRating = $businessReviews->avg('rating');
        $avgEmployeeRating = $employeeReviews->avg('rating');

        return view('business.reviews.index', compact(
            'business',
            'businessReviews',
            'employeeReviews',
            'avgBusinessRating',
            'avgEmployeeRating'
        ));
    }
}
