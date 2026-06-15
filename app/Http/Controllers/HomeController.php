<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessReview;

class HomeController extends Controller
{
    public function index()
    {
        $minVotes = 3;
        $globalAvg = (float) (BusinessReview::avg('rating') ?? 0);

        $popularBusinesses = Business::where('is_approved', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withMin('services', 'price')
            ->get()
            ->sortByDesc(function ($business) use ($minVotes, $globalAvg) {
                $votes = $business->reviews_count;
                $avg = $business->reviews_avg_rating ?? 0;

                return ($votes / ($votes + $minVotes)) * $avg + ($minVotes / ($votes + $minVotes)) * $globalAvg;
            })
            ->take(6)
            ->values();

        $categories = [
            ['name' => 'Fryzjer',        'icon' => 'bi-scissors'],
            ['name' => 'Barber',         'icon' => 'bi-person'],
            ['name' => 'Kosmetyczka',    'icon' => 'bi-flower1'],
            ['name' => 'Paznokcie',      'icon' => 'bi-hand-index'],
            ['name' => 'Masaż',          'icon' => 'bi-droplet'],
            ['name' => 'Brwi i rzęsy',   'icon' => 'bi-eye'],
        ];

        return view('home', compact('popularBusinesses', 'categories'));
    }
}
