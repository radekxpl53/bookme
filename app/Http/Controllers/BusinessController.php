<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Auth::user()->businesses;
        return view('business.index', compact('businesses'));
    }

    public function create()
    {
        return view('business.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ], [
            'lat.required' => 'Musisz zaznaczyć lokalizację salonu na mapie!',
            'lon.required' => 'Musisz zaznaczyć lokalizację salonu na mapie!',
        ]);

        $validated['owner_id'] = Auth::id();

        Business::create($validated);

        return redirect()->route('biznes.lokale.index')
                         ->with('success', 'Twój nowy salon został pomyślnie dodany!');
    }

    public function edit($id)
    {
        $business = Business::findOrFail($id);
        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień. Nie możesz edytować obcego salonu!');
        }

        return view('business.edit', compact('business'));
    }

    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień. Nie możesz edytować obcego salonu!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        $business->update($validated);

        return redirect()->route('biznes.lokale.index')
                         ->with('success', 'Dane salonu zostały pomyślnie zaktualizowane!');
    }

    public function destroy($id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień. Nie możesz usunąć obcego salonu!');
        }

        $business->delete();

        return redirect()->route('biznes.lokale.index')
                         ->with('success', 'Salon został usunięty z systemu!');
    }

}
