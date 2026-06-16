<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessBlacklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlacklistController extends Controller
{
    private function authorizeOwner(Business $business): void
    {
        $user = Auth::user();
        if ($business->owner_id !== $user->id && !$user->is_admin) {
            abort(403);
        }
    }

    public function index(Business $business)
    {
        $this->authorizeOwner($business);

        $blacklist = $business->blacklist()->with('user')->latest('created_at')->get();

        return view('business.blacklist.index', compact('business', 'blacklist'));
    }

    public function create(Business $business)
    {
        $this->authorizeOwner($business);

        return view('business.blacklist.create', compact('business'));
    }

    public function store(Request $request, Business $business)
    {
        $this->authorizeOwner($business);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'nullable|string|max:500',
        ], [
            'email.exists' => 'Nie znaleziono użytkownika z takim adresem e-mail.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['email' => 'Nie możesz zablokować samego siebie.'])->withInput();
        }

        $exists = BusinessBlacklist::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Ten użytkownik jest już na czarnej liście.'])->withInput();
        }

        BusinessBlacklist::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->route('biznes.lokale.blacklist.index', $business)
                         ->with('success', 'Użytkownik został dodany do czarnej listy.');
    }

    public function destroy(Business $business, BusinessBlacklist $blacklist)
    {
        $this->authorizeOwner($business);

        if ($blacklist->business_id !== $business->id) {
            abort(404);
        }

        $blacklist->delete();

        return redirect()->route('biznes.lokale.blacklist.index', $business)
                         ->with('success', 'Użytkownik został usunięty z czarnej listy.');
    }
}
