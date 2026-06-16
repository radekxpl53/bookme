<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessBlacklist;
use App\Models\Employee;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create(Request $request, Business $business, AvailabilityService $availability)
    {
        abort_if(! $business->is_approved, 404);

        $business->load([
            'services',
            'employees' => fn ($q) => $q->where('is_active', true),
            'employees.services',
            'employees.workingHours',
        ]);

        $blacklisted = $this->isBlacklisted($business->id);

        $service = $request->filled('service_id')
            ? $business->services->firstWhere('id', (int) $request->input('service_id'))
            : null;

        $specialists = $service
            ? $business->employees->filter(fn ($e) => $e->services->contains('id', $service->id))->values()
            : collect();

        $employee = null;
        if ($service && $request->filled('employee_id')) {
            $employee = $specialists->firstWhere('id', (int) $request->input('employee_id'));
        }

        $day = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : Carbon::today();

        $time = $request->input('time');

        if ($service && ! $time) {
            foreach ($specialists as $e) {
                $e->slots = $availability->findSlots($service, $day, null, null, 100, $e->id);
            }
        }

        if (! $service) {
            $step = 1;
        } elseif (! $employee || ! $time) {
            $step = 2;
        } else {
            $step = 3;
        }

        return view('booking.create', compact(
            'business', 'service', 'specialists', 'employee', 'day', 'time', 'step', 'blacklisted'
        ));
    }

    public function store(Request $request, AvailabilityService $availability)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addMonths(6)->toDateString()],
            'time' => 'required|date_format:H:i',
        ], [
            'date.before_or_equal' => 'Można rezerwować maksymalnie pół roku naprzód.',
            'date.after_or_equal' => 'Nie można rezerwować w przeszłości.',
        ]);

        $service = Service::with('business')->findOrFail($validated['service_id']);
        $employee = Employee::with(['workingHours', 'services'])->findOrFail($validated['employee_id']);

        $start = Carbon::parse($validated['date'].' '.$validated['time']);

        $problems = [];
        if (! $service->business->is_approved) {
            $problems[] = 'Ten lokal nie jest dostępny do rezerwacji.';
        }
        if ($this->isBlacklisted($service->business_id)) {
            $problems[] = 'Nie możesz rezerwować wizyt w tym lokalu.';
        }
        if ($employee->business_id !== $service->business_id) {
            $problems[] = 'Wybrany pracownik nie należy do tego lokalu.';
        }
        if (! $employee->services->contains('id', $service->id)) {
            $problems[] = 'Wybrany pracownik nie wykonuje tej usługi.';
        }
        if (! $availability->isAvailable($service, $employee, $start)) {
            $problems[] = 'Ten termin jest już niedostępny. Wybierz inny.';
        }

        if (! empty($problems)) {
            return back()->withErrors($problems)->withInput();
        }

        $appointment = Appointment::create([
            'client_id' => Auth::id(),
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'start_at' => $start,
            'finish_at' => $start->copy()->addMinutes($service->duration_minutes),
            'status' => 'pending',
            'total_price' => $service->price,
        ]);

        return redirect()->route('rezerwacja.success', $appointment);
    }

    public function success(Appointment $appointment)
    {
        if ($appointment->client_id !== Auth::id()) {
            abort(403);
        }

        $appointment->load(['service.business', 'employee']);

        return view('booking.success', compact('appointment'));
    }

    private function isBlacklisted(int $businessId): bool
    {
        return BusinessBlacklist::where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->exists();
    }
}

