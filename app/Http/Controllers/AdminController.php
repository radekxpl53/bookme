<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingBusinesses = Business::where('is_approved', false)->count();
        $totalBusinesses = Business::count();
        $totalUsers = User::count();
        $totalOwners = User::whereHas('businesses')->count();


        $totalAppointments = \App\Models\Appointment::count();
        $completedAppointments = \App\Models\Appointment::where('status', 'completed')->count();
        $completionRate = $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100) : 0;


        $totalBusinessReviews = \App\Models\BusinessReview::count();
        $totalEmployeeReviews = \App\Models\EmployeeReview::count();
        $totalReviews = $totalBusinessReviews + $totalEmployeeReviews;

        return view('admin.dashboard', compact(
            'pendingBusinesses', 'totalBusinesses', 'totalUsers', 'totalOwners',
            'totalAppointments', 'completedAppointments', 'completionRate',
            'totalReviews'
        ));
    }

    public function businesses(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = Business::with('owner')->latest();

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }

        $businesses = $query->get();

        return view('admin.businesses', compact('businesses', 'status', 'search'));
    }

    public function approveBusiness(Business $business)
    {
        $business->update(['is_approved' => true]);
        return back()->with('success', "Lokal {$business->name} został zatwierdzony!");
    }

    public function rejectBusiness(Business $business)
    {
        $business->delete();
        return back()->with('success', "Lokal został odrzucony i usunięty ze zgłoszeń.");
    }

    public function editBusiness(Business $business)
    {
        return view('admin.businesses.edit', compact('business'));
    }

    public function updateBusiness(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_approved' => 'boolean',
        ]);

        $business->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? '',
            'is_approved' => $request->has('is_approved'),
        ]);

        return redirect()->route('admin.businesses')->with('success', 'Lokal został zaktualizowany.');
    }

    public function destroyBusiness(Business $business)
    {
        $business->delete();
        return back()->with('success', 'Lokal został pomyślnie usunięty.');
    }

    public function users(Request $request)
    {
        $role = $request->get('role', 'all');
        $search = $request->get('search');

        $query = User::withCount('businesses');

        if ($role === 'owners') {
            $query->whereHas('businesses');
        } elseif ($role === 'clients') {
            $query->whereDoesntHave('businesses')->where('is_admin', false);
        } elseif ($role === 'admins') {
            $query->where('is_admin', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->get();

        return view('admin.users', compact('users', 'role', 'search'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['is_admin'] = $request->has('is_admin');

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    public function destroyUser(User $user)
    {

        $user->delete();

        return back()->with('success', 'Użytkownik został pomyślnie usunięty.');
    }

    public function reviews(Request $request)
    {
        $type = $request->get('type', 'all'); // 'business', 'employee', 'all'
        $search = $request->get('search');
        $rating = $request->get('rating');

        $reviews = collect();

        if ($type === 'all' || $type === 'business') {
            $q = \App\Models\BusinessReview::with(['business', 'user'])->latest('created_at');
            if ($rating) $q->where('rating', $rating);
            if ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('comment', 'like', "%{$search}%")
                        ->orWhereHas('user', function($u) use ($search) {
                            $u->where('username', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('surname', 'like', "%{$search}%");
                        })
                        ->orWhereHas('business', function($b) use ($search) {
                            $b->where('name', 'like', "%{$search}%");
                        });
                });
            }
            $businessReviews = $q->get()->map(function($item) {
                $item->review_type = 'business';
                $item->target_name = $item->business->name;
                return $item;
            });
            $reviews = $reviews->concat($businessReviews);
        }

        if ($type === 'all' || $type === 'employee') {
            $q = \App\Models\EmployeeReview::with(['employee', 'user'])->latest('created_at');
            if ($rating) $q->where('rating', $rating);
            if ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('comment', 'like', "%{$search}%")
                        ->orWhereHas('user', function($u) use ($search) {
                            $u->where('username', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('surname', 'like', "%{$search}%");
                        })
                        ->orWhereHas('employee', function($e) use ($search) {
                            $e->where('name', 'like', "%{$search}%");
                        });
                });
            }
            $employeeReviews = $q->get()->map(function($item) {
                $item->review_type = 'employee';
                $item->target_name = $item->employee->name;
                return $item;
            });
            $reviews = $reviews->concat($employeeReviews);
        }

        $reviews = $reviews->sortByDesc('created_at');

        return view('admin.reviews', compact('reviews', 'type', 'search', 'rating'));
    }
    
    public function editReview($type, $id)
    {
        if ($type === 'business') {
            $review = \App\Models\BusinessReview::findOrFail($id);
            $targetName = $review->business->name;
        } else {
            $review = \App\Models\EmployeeReview::findOrFail($id);
            $targetName = $review->employee->name;
        }

        return view('admin.reviews.edit', compact('review', 'type', 'targetName'));
    }

    public function updateReview(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($type === 'business') {
            $review = \App\Models\BusinessReview::findOrFail($id);
            $review->update($validated);
        } else {
            $review = \App\Models\EmployeeReview::findOrFail($id);
            $review->update($validated);
        }

        return redirect()->route('admin.reviews')->with('success', 'Opinia została pomyślnie zaktualizowana.');
    }

    public function destroyReview($type, $id)
    {
        if ($type === 'business') {
            \App\Models\BusinessReview::findOrFail($id)->delete();
        } elseif ($type === 'employee') {
            \App\Models\EmployeeReview::findOrFail($id)->delete();
        }

        return back()->with('success', 'Opinia została pomyślnie usunięta.');
    }

    public function appointments(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = \App\Models\Appointment::with(['employee.business', 'client', 'service']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('surname', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('employee.business', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('employee', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $appointments = $query->latest('start_at')->get();

        return view('admin.appointments', compact('appointments', 'status', 'search'));
    }

    public function editAppointment(\App\Models\Appointment $appointment)
    {
        $appointment->load(['employee.business.services', 'employee.business.employees']);
        return view('admin.appointments.edit', compact('appointment'));
    }

    public function updateAppointment(Request $request, \App\Models\Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $service = \App\Models\Service::findOrFail($validated['service_id']);
        
        // Calculate start_at and finish_at based on service duration
        $startAt = \Carbon\Carbon::parse($validated['appointment_date'] . ' ' . $validated['start_time']);
        $finishAt = $startAt->copy()->addMinutes($service->duration);
        
        $appointment->update([
            'status' => $validated['status'],
            'employee_id' => $validated['employee_id'],
            'service_id' => $validated['service_id'],
            'start_at' => $startAt,
            'finish_at' => $finishAt,
            'total_price' => $service->price,
        ]);

        return redirect()->route('admin.appointments')->with('success', 'Wizyta została pomyślnie zaktualizowana.');
    }

    public function destroyAppointment(\App\Models\Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('success', 'Wizyta została bezpowrotnie usunięta.');
    }
}
