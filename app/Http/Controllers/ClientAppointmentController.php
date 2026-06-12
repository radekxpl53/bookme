<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class ClientAppointmentController extends Controller
{
    public function index()
    {
        $appointments = Auth::user()->appointments()
            ->with(['service.business', 'employee'])
            ->orderByDesc('start_at')
            ->get();

        $nadchodzace = $appointments
            ->filter(fn ($a) => $a->start_at->isFuture() && in_array($a->status, ['pending', 'confirmed']))
            ->sortBy('start_at')
            ->values();

        $historia = $appointments
            ->reject(fn ($a) => $a->start_at->isFuture() && in_array($a->status, ['pending', 'confirmed']))
            ->values();

        $ocenieniPracownicy = Auth::user()->employeeReviews->pluck('employee_id');

        return view('client.appointments', compact('nadchodzace', 'historia', 'ocenieniPracownicy'));
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->client_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($appointment->status, ['pending', 'confirmed']) || ! $appointment->start_at->isFuture()) {
            return back()->withErrors('Tej wizyty nie można już anulować.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Wizyta została anulowana.');
    }
}
