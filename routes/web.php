<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessPublicController;
use App\Http\Controllers\ProfileController;

// DO USUNIĘCIA POTEM, JAK ZADZIAŁA ZWYKLE LOGOWANIE
Route::get('/dev-login', function () {
    $owner = User::where('email', 'wlasciciel@bookme.test')->first();
    Auth::login($owner);
    return redirect()->route('biznes.lokale.index');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lokal/{business}', [BusinessPublicController::class, 'show'])->name('lokal.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'owner'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::resource('lokale', BusinessController::class);
});

require __DIR__.'/auth.php';
