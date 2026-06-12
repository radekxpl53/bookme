<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLeaveController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }
    }

    public function index(Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $leaves = $employee->leaves()->orderBy('start_date', 'desc')->get();

        return view('business.employees.leaves.index', compact('business', 'employee', 'leaves'));
    }

    public function store(Request $request, Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        $employee->leaves()->create($validated);

        return redirect()->route('biznes.lokale.pracownicy.urlopy.index', [$business, $employee])
                         ->with('success', 'Urlop został poprawnie dodany.');
    }

    public function destroy(Business $business, Employee $employee, EmployeeLeave $urlopy)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id || $urlopy->employee_id !== $employee->id) {
            abort(404);
        }

        $urlopy->delete();

        return redirect()->route('biznes.lokale.pracownicy.urlopy.index', [$business, $employee])
                         ->with('success', 'Urlop został usunięty.');
    }
}
