<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LokalOpiniaController extends Controller
{
    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        BusinessReview::updateOrCreate(
            ['business_id' => $business->id, 'user_id' => Auth::id()],
            ['rating' => $validated['rating'], 'comment' => $validated['comment'] ?? null]
        );

        return redirect()->route('lokal.show', $business)
            ->with('success', 'Dziękujemy za opinię o lokalu!');
    }
}
