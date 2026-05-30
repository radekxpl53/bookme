<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;

// DO USUNIĘCIA POTEM, JAK ZROBIMY ZWYKLE LOGOWANIE
Route::get('/dev-login', function () {
    $owner = User::where('email', 'wlasciciel@bookme.test')->first();
    Auth::login($owner);
    return redirect()->route('biznes.lokale.index');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'owner'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::resource('lokale', BusinessController::class);
});
