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

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    public function destroyUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Nie możesz usunąć własnego konta!');
        }

        $user->delete();

        return back()->with('success', 'Użytkownik został pomyślnie usunięty.');
    }
}
