<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(Business $business)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        $services = $business->services;

        return view('business.services.index', compact('business', 'services'));
    }

    public function create(Business $business)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('business.services.create', compact('business'));
    }

    public function store(Request $request, Business $business)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $validated['business_id'] = $business->id;

        Service::create($validated);

        return redirect()->route('biznes.uslugi.index', $business)
                         ->with('success', 'Usługa została dodana!');
    }

    public function edit(Business $business, Service $service)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        return view('business.services.edit', compact('business', 'service'));
    }

    public function update(Request $request, Business $business, Service $service)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $service->update($validated);

        return redirect()->route('biznes.uslugi.index', $business)
                         ->with('success', 'Usługa została zaktualizowana!');
    }

    public function destroy(Business $business, Service $service)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($service->business_id !== $business->id) {
            abort(404);
        }

        $service->delete();

        return redirect()->route('biznes.uslugi.index', $business)
                         ->with('success', 'Usługa została usunięta!');
    }
}
