<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessPhotoController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień. Nie możesz edytować zdjęć obcego salonu!');
        }
    }

    public function index(Business $business)
    {
        $this->authorizeOwner($business);
        
        $photos = $business->photos()->latest()->get();

        return view('business.photos.index', compact('business', 'photos'));
    }

    public function store(Request $request, Business $business)
    {
        $this->authorizeOwner($business);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('business_photos', 'public');

                BusinessPhoto::create([
                    'business_id' => $business->id,
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Zdjęcia zostały dodane do portfolio lokalu!');
    }

    public function destroy(Business $business, BusinessPhoto $photo)
    {
        $this->authorizeOwner($business);

        if ($photo->business_id !== $business->id) {
            abort(404);
        }

        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return redirect()->back()->with('success', 'Zdjęcie zostało usunięte z portfolio!');
    }
}
