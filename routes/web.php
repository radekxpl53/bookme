<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessPublicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientAppointmentController;
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

    Route::get('/rezerwacja/sukces/{appointment}', [BookingController::class, 'success'])->name('rezerwacja.success');
    Route::get('/rezerwacja/{business}', [BookingController::class, 'create'])->name('rezerwacja.create');
    Route::post('/rezerwacja', [BookingController::class, 'store'])->name('rezerwacja.store');

    Route::get('/moje-wizyty', [ClientAppointmentController::class, 'index'])->name('klient.wizyty.index');
    Route::patch('/moje-wizyty/{appointment}/anuluj', [ClientAppointmentController::class, 'cancel'])->name('klient.wizyty.anuluj');
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
    Route::resource('lokale.pracownicy.urlopy', \App\Http\Controllers\EmployeeLeaveController::class)->only(['index', 'store', 'destroy'])->parameters(['lokale' => 'business', 'pracownicy' => 'employee', 'urlopy' => 'urlopy']);
    
    Route::get('lokale/{business}/kalendarz', [App\Http\Controllers\BusinessCalendarController::class, 'index'])->name('lokale.kalendarz.index');
    Route::get('lokale/{business}/kalendarz/events', [App\Http\Controllers\BusinessCalendarController::class, 'events'])->name('lokale.kalendarz.events');
    Route::post('lokale/{business}/kalendarz/appointments/{appointment}/status', [App\Http\Controllers\BusinessCalendarController::class, 'updateStatus'])->name('lokale.kalendarz.status');
    Route::get('lokale/{business}/opinie', [\App\Http\Controllers\BusinessReviewController::class, 'index'])->name('lokale.opinie.index');
});



Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/biznesy', [AdminController::class, 'businesses'])->name('businesses');
    Route::post('/biznesy/{business}/approve', [AdminController::class, 'approveBusiness'])->name('businesses.approve');
    Route::post('/biznesy/{business}/reject', [AdminController::class, 'rejectBusiness'])->name('businesses.reject');
    Route::get('/uzytkownicy', [AdminController::class, 'users'])->name('users');
});

require __DIR__.'/auth.php';
