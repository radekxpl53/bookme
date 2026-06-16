<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employee;
use App\Models\EmployeePortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeePortfolioController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        $user = Auth::user();
        if ($business->owner_id !== $user->id && !$user->is_admin) {
            abort(403, 'Brak uprawnień. Nie możesz edytować obcego salonu!');
        }
    }

    public function index(Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }
        
        $photos = $employee->portfolio()->latest()->get();

        return view('business.employees.portfolio', compact('business', 'employee', 'photos'));
    }

    public function store(Request $request, Business $business, Employee $employee)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id) {
            abort(404);
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('employee_portfolio', 'public');

                EmployeePortfolio::create([
                    'employee_id' => $employee->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Zdjęcia zostały dodane do portfolio pracownika!');
    }

    public function destroy(Business $business, Employee $employee, EmployeePortfolio $portfolio)
    {
        $this->authorizeOwner($business);

        if ($employee->business_id !== $business->id || $portfolio->employee_id !== $employee->id) {
            abort(404);
        }

        if (Storage::disk('public')->exists($portfolio->path)) {
            Storage::disk('public')->delete($portfolio->path);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Zdjęcie zostało usunięte z portfolio pracownika!');
    }
}
