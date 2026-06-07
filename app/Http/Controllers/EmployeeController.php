<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employee;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }
    }

    public function index(Business $business)
    {
        $this->authorizeOwner($business);

        $employees = $business->employees()->withCount('services')->get();

        return view('business.employees.index', compact('business', 'employees'));
    }

    public function create(Business $business)
    {
        $this->authorizeOwner($business);

        $services = $business->services;

        return view('business.employees.create', compact('business', 'services'));
    }

    public function store(Request $request, Business $business)
    {
        $this->authorizeOwner($business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
            'working_hours.*.start_time' => 'nullable|date_format:H:i',
            'working_hours.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $employee = Employee::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'specialization' => $validated['specialization'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['services'])) {
            $employee->services()->sync($validated['services']);
        }

        if (!empty($validated['working_hours'])) {
            foreach ($validated['working_hours'] as $day => $hours) {
                if (!empty($hours['start_time']) && !empty($hours['end_time'])) {
                    WorkingHour::create([
                        'employee_id' => $employee->id,
                        'day_of_week' => $day,
                        'start_time' => $hours['start_time'],
                        'end_time' => $hours['end_time'],
                    ]);
                }
            }
        }

        return redirect()->route('biznes.lokale.pracownicy.index', $business)
                         ->with('success', 'Pracownik został dodany!');
    }

    public function edit(Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $services = $business->services;
        $assignedServiceIds = $employee->services->pluck('id')->toArray();
        $workingHours = $employee->workingHours->keyBy('day_of_week');

        return view('business.employees.edit', compact('business', 'employee', 'services', 'assignedServiceIds', 'workingHours'));
    }

    public function update(Request $request, Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'working_hours' => 'nullable|array',
            'working_hours.*.start_time' => 'nullable|date_format:H:i',
            'working_hours.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $employee->update([
            'name' => $validated['name'],
            'specialization' => $validated['specialization'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $employee->services()->sync($validated['services'] ?? []);

        $employee->workingHours()->delete();
        if (!empty($validated['working_hours'])) {
            foreach ($validated['working_hours'] as $day => $hours) {
                if (!empty($hours['start_time']) && !empty($hours['end_time'])) {
                    WorkingHour::create([
                        'employee_id' => $employee->id,
                        'day_of_week' => $day,
                        'start_time' => $hours['start_time'],
                        'end_time' => $hours['end_time'],
                    ]);
                }
            }
        }

        return redirect()->route('biznes.lokale.pracownicy.index', $business)
                         ->with('success', 'Dane pracownika zostały zaktualizowane!');
    }

    public function destroy(Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $employee->workingHours()->delete();
        $employee->services()->detach();
        DB::table('employee_reviews_images')->where('employee_id', $employee->id)->delete();
        $employee->reviews()->delete();
        $employee->portfolio()->delete();
        $employee->appointments()->delete();
        $employee->delete();

        return redirect()->route('biznes.lokale.pracownicy.index', $business)
                         ->with('success', 'Pracownik został usunięty!');
    }
}
