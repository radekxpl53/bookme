<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessReview;
use App\Models\EmployeeReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function storeBusinessReview(Request $request, Business $business)
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

    public function create(Appointment $appointment)
    {
        $this->ensureCanReview($appointment);

        $appointment->load(['service.business', 'employee.reviewImages']);

        $review = EmployeeReview::where('employee_id', $appointment->employee_id)
            ->where('user_id', Auth::id())
            ->first();

        return view('client.review', compact('appointment', 'review'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $this->ensureCanReview($appointment);

        $appointment->load('service');

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        EmployeeReview::updateOrCreate(
            ['employee_id' => $appointment->employee_id, 'user_id' => Auth::id()],
            [
                'service' => $appointment->service->name,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()->route('klient.opinia.create', $appointment)
            ->with('success', 'Dziękujemy za opinię! Jeśli chcesz, dodaj zdjęcie.');
    }

    private function ensureCanReview(Appointment $appointment): void
    {
        if ($appointment->client_id !== Auth::id() || $appointment->status !== 'completed') {
            abort(403);
        }
    }
}
