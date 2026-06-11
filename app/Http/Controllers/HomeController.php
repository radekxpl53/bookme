<?php

namespace App\Http\Controllers;

use App\Models\Business;

class HomeController extends Controller
{
    public function index()
    {
        $popularBusinesses = Business::where('is_approved', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->take(6)
            ->get();

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
