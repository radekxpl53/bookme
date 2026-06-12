<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessCalendarController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień.');
        }
    }

    public function index(Business $business)
    {
        $this->authorizeOwner($business);

        $employees = $business->employees()->where('is_active', true)->get();

        return view('business.calendar.index', compact('business', 'employees'));
    }

    public function events(Request $request, Business $business)
    {
        $this->authorizeOwner($business);

        $query = Appointment::with(['client', 'service', 'employee'])
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            });

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($apt) {
            $color = $this->getColorForEmployee($apt->employee_id);
            if ($apt->status === 'cancelled') {
                $color = '#6c757d'; // grey out cancelled
            }

            return [
                'id' => $apt->id,
                'title' => $apt->client->first_name . ' ' . $apt->client->surname . ' - ' . $apt->service->name,
                'start' => $apt->start_at->format('Y-m-d\TH:i:s'),
                'end' => $apt->finish_at->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'client_name' => $apt->client->first_name . ' ' . $apt->client->surname,
                    'client_phone' => $apt->client->phone ?? 'Brak numeru',
                    'service_name' => $apt->service->name,
                    'employee_name' => $apt->employee->name,
                    'status' => $apt->status,
                    'price' => $apt->total_price,
                ]
            ];
        });

        return response()->json($events);
    }

    public function updateStatus(Request $request, Business $business, Appointment $appointment)
    {
        $this->authorizeOwner($business);

        if ($appointment->employee->business_id !== $business->id) {
            abort(404);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status wizyty został zaktualizowany.');
    }

    private function getColorForEmployee($employeeId)
    {
        $colors = [
            '#0d6efd', // blue
            '#198754', // green
            '#dc3545', // red
            '#fd7e14', // orange
            '#6f42c1', // purple
            '#d63384', // pink
            '#20c997', // teal
            '#0dcaf0', // cyan
        ];
        return $colors[$employeeId % count($colors)];
    }
}
