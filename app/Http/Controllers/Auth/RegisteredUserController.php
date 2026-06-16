<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username'   => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'first_name' => ['required', 'string', 'max:255'],
            'surname'    => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'regex:/^(\+48)?[0-9]{9}$/'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'Podaj poprawny numer telefonu: 9 cyfr, opcjonalnie z prefiksem +48.',
        ]);

        $user = User::create([
            'username'   => $request->username,
            'first_name' => $request->first_name,
            'surname'    => $request->surname,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'password'   => $request->password,
            'is_admin'   => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home');
    }
}
