<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAppointmentController extends Controller
{
    public function index()
    {
        $appointments = Auth::user()->appointments()
            ->with(['service.business', 'employee'])
            ->orderByDesc('start_at')
            ->get();

        $upcoming = $appointments
            ->filter(fn ($a) => $a->start_at->isFuture() && in_array($a->status, ['pending', 'confirmed']))
            ->sortBy('start_at')
            ->values();

        $history = $appointments
            ->reject(fn ($a) => $a->start_at->isFuture() && in_array($a->status, ['pending', 'confirmed']))
            ->values();

        $reviewedEmployees = Auth::user()->employeeReviews->pluck('employee_id');

        return view('client.appointments', compact('upcoming', 'history', 'reviewedEmployees'));
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

    public function reschedule(Request $request, Appointment $appointment, AvailabilityService $availability)
    {
        $this->ensureReschedulable($appointment);

        $appointment->load(['service.business', 'employee']);

        $day = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : Carbon::today();

        $slots = $availability->findSlots(
            $appointment->service, $day, null, null, 50, $appointment->employee_id, $appointment->id
        );

        return view('client.reschedule', compact('appointment', 'day', 'slots'));
    }

    public function rescheduleStore(Request $request, Appointment $appointment, AvailabilityService $availability)
    {
        $this->ensureReschedulable($appointment);

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addMonths(6)->toDateString()],
            'time' => 'required|date_format:H:i',
        ], [
            'date.before_or_equal' => 'Można rezerwować maksymalnie pół roku naprzód.',
            'date.after_or_equal' => 'Nie można rezerwować w przeszłości.',
        ]);

        $appointment->load(['service', 'employee.workingHours']);

        $start = Carbon::parse($validated['date'].' '.$validated['time']);

        if (! $availability->isAvailable($appointment->service, $appointment->employee, $start, $appointment->id)) {
            return back()->withErrors('Ten termin jest już niedostępny. Wybierz inny.');
        }

        $appointment->update([
            'start_at' => $start,
            'finish_at' => $start->copy()->addMinutes($appointment->service->duration_minutes),
            'status' => 'pending',
        ]);

        return redirect()->route('klient.wizyty.index')
            ->with('success', 'Termin wizyty został zmieniony.');
    }

    private function ensureReschedulable(Appointment $appointment): void
    {
        if ($appointment->client_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($appointment->status, ['pending', 'confirmed']) || ! $appointment->start_at->isFuture()) {
            abort(403);
        }
    }
}
