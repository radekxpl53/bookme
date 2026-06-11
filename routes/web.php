<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessPublicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;

// DO USUNIĘCIA POTEM, JAK ZADZIAŁA ZWYKLE LOGOWANIE
Route::get('/dev-login', function () {
    $owner = User::where('email', 'wlasciciel@bookme.test')->first();
    Auth::login($owner);
    return redirect()->route('biznes.lokale.index');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/szukaj', [SearchController::class, 'index'])->name('szukaj');

Route::get('/lokal/{business}', [BusinessPublicController::class, 'show'])->name('lokal.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/rezerwacja', function (\Illuminate\Http\Request $request) {
        $service = \App\Models\Service::with('business')->findOrFail($request->input('usluga_id'));
        $employee = \App\Models\Employee::findOrFail($request->input('pracownik_id'));

        return view('booking.stub', [
            'service' => $service,
            'employee' => $employee,
            'termin' => $request->input('termin'),
        ]);
    })->name('rezerwacja.stub');
});

Route::middleware(['auth'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::get('lokale/create', [BusinessController::class, 'create'])->name('lokale.create');
    Route::post('lokale', [BusinessController::class, 'store'])->name('lokale.store');
});

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BlacklistController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusinessPhotoController;
use App\Http\Controllers\EmployeePortfolioController;

Route::middleware(['auth', 'owner'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::resource('lokale', BusinessController::class)->except(['create', 'store']);
    Route::resource('lokale.uslugi', ServiceController::class)->except(['show'])->parameters(['lokale' => 'business', 'uslugi' => 'service']);
    Route::resource('lokale.pracownicy', EmployeeController::class)->except(['show'])->parameters(['lokale' => 'business', 'pracownicy' => 'employee']);
    Route::resource('lokale.blacklist', BlacklistController::class)->except(['show', 'edit', 'update'])->parameters(['lokale' => 'business', 'blacklist' => 'blacklist']);
    
    Route::resource('lokale.zdjecia', BusinessPhotoController::class)->only(['index', 'store', 'destroy'])->parameters(['lokale' => 'business', 'zdjecia' => 'photo']);
    Route::resource('lokale.pracownicy.portfolio', EmployeePortfolioController::class)->only(['index', 'store', 'destroy'])->parameters(['lokale' => 'business', 'pracownicy' => 'employee', 'portfolio' => 'portfolio']);
});



Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/biznesy', [AdminController::class, 'businesses'])->name('businesses');
    Route::post('/biznesy/{business}/approve', [AdminController::class, 'approveBusiness'])->name('businesses.approve');
    Route::post('/biznesy/{business}/reject', [AdminController::class, 'rejectBusiness'])->name('businesses.reject');
    Route::get('/uzytkownicy', [AdminController::class, 'users'])->name('users');
});

require __DIR__.'/auth.php';
