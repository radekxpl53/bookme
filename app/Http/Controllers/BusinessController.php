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

}
