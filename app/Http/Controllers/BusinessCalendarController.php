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
        $user = Auth::user();
        if ($business->owner_id !== $user->id && !$user->is_admin) {
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
            $textColor = '#ffffff';
            if ($apt->status === 'cancelled') {
                $color = '#6c757d';
            } elseif ($apt->status === 'pending') {
                $color = '#ffc107';
                $textColor = '#000000';
            } elseif ($apt->status === 'completed') {
                $color = '#198754'; // Zakończona - zielony
            }

            return [
                'id' => $apt->id,
                'title' => $apt->client->first_name . ' ' . $apt->client->surname . ' - ' . $apt->service->name,
                'start' => $apt->start_at->format('Y-m-d\TH:i:s'),
                'end' => $apt->finish_at->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'display' => 'block', // Zmienia wygląd kropek na pełne bloki w kalendarzu miesięcznym
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
            '#0d6efd',
            '#6610f2', // Zastąpiono zielony (#198754) indygo, żeby zielony był tylko dla zakończonych
            '#dc3545',
            '#fd7e14',
            '#6f42c1',
            '#d63384',
            '#20c997',
            '#0dcaf0',
        ];
        return $colors[$employeeId % count($colors)];
    }
}
