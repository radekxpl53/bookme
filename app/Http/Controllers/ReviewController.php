<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\EmployeeReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Appointment $appointment)
    {
        $this->sprawdzDostep($appointment);

        $appointment->load(['service.business', 'employee.reviewImages']);

        $opinia = EmployeeReview::where('employee_id', $appointment->employee_id)
            ->where('user_id', Auth::id())
            ->first();

        return view('client.review', compact('appointment', 'opinia'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $this->sprawdzDostep($appointment);

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

    private function sprawdzDostep(Appointment $appointment): void
    {
        if ($appointment->client_id !== Auth::id() || $appointment->status !== 'completed') {
            abort(403);
        }
    }
}
