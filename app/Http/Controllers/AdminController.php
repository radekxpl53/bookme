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

        return view('admin.dashboard', compact(
            'pendingBusinesses', 'totalBusinesses', 'totalUsers', 'totalOwners'
        ));
    }

    public function businesses(Request $request)
    {
        $status = $request->get('status', 'pending');

        if ($status === 'pending') {
            $businesses = Business::where('is_approved', false)->latest()->get();
        } elseif ($status === 'approved') {
            $businesses = Business::where('is_approved', true)->latest()->get();
        } else {
            $businesses = Business::latest()->get();
        }

        return view('admin.businesses', compact('businesses', 'status'));
    }

    public function approveBusiness(Business $business)
    {
        $business->update(['is_approved' => true]);
        return back()->with('success', "Lokal {$business->name} został zatwierdzony!");
    }

    public function rejectBusiness(Business $business)
    {
        // Opcjonalnie mozna dodac powod odrzucenia (mail do uzytkownika), na razie po prostu usuwamy
        $business->delete();
        return back()->with('success', "Lokal został odrzucony i usunięty ze zgłoszeń.");
    }

    public function users(Request $request)
    {
        $role = $request->get('role', 'all');

        $query = User::query();

        if ($role === 'owners') {
            $query->whereHas('businesses');
        } elseif ($role === 'clients') {
            $query->whereDoesntHave('businesses')->where('is_admin', false);
        } elseif ($role === 'admins') {
            $query->where('is_admin', true);
        }

        $users = $query->latest()->get();

        return view('admin.users', compact('users', 'role'));
    }
}
