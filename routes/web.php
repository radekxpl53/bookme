<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessPublicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientAppointmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusinessPhotoController;
use App\Http\Controllers\EmployeePortfolioController;

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
    Route::get('/moje-wizyty/{appointment}/zmien-termin', [ClientAppointmentController::class, 'reschedule'])->name('klient.wizyty.zmien');
    Route::patch('/moje-wizyty/{appointment}/zmien-termin', [ClientAppointmentController::class, 'rescheduleStore'])->name('klient.wizyty.zmien.zapisz');

    Route::get('/moje-wizyty/{appointment}/opinia', [ReviewController::class, 'create'])->name('klient.opinia.create');
    Route::post('/moje-wizyty/{appointment}/opinia', [ReviewController::class, 'store'])->name('klient.opinia.store');
    Route::post('/moje-wizyty/{appointment}/opinia/zdjecie', [ReviewImageController::class, 'storeEmployee'])->name('klient.opinia.zdjecie');

    Route::post('/lokal/{business}/opinia', [ReviewController::class, 'storeBusinessReview'])->name('lokal.opinia.store');
    Route::post('/lokal/{business}/opinia/zdjecie', [ReviewImageController::class, 'storeBusiness'])->name('lokal.opinia.zdjecie');
});

Route::middleware(['auth'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::get('lokale/create', [BusinessController::class, 'create'])->name('lokale.create');
    Route::post('lokale', [BusinessController::class, 'store'])->name('lokale.store');
});



Route::middleware(['auth', 'owner'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::resource('lokale', BusinessController::class)->except(['create', 'store']);
    Route::resource('lokale.uslugi', ServiceController::class)->except(['show'])->parameters(['lokale' => 'business', 'uslugi' => 'service']);
    Route::resource('lokale.pracownicy', EmployeeController::class)->except(['show'])->parameters(['lokale' => 'business', 'pracownicy' => 'employee']);
    Route::resource('lokale.blacklist', BlacklistController::class)->except(['show'])->parameters(['lokale' => 'business', 'blacklist' => 'blacklist']);
    
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
    Route::get('/biznesy/{business}/edit', [AdminController::class, 'editBusiness'])->name('businesses.edit');
    Route::put('/biznesy/{business}', [AdminController::class, 'updateBusiness'])->name('businesses.update');
    Route::delete('/biznesy/{business}', [AdminController::class, 'destroyBusiness'])->name('businesses.destroy');
    Route::get('/uzytkownicy', [AdminController::class, 'users'])->name('users');
    Route::get('/uzytkownicy/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/uzytkownicy/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/uzytkownicy/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/opinie', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/opinie/{type}/{id}/edit', [AdminController::class, 'editReview'])->name('reviews.edit');
    Route::put('/opinie/{type}/{id}', [AdminController::class, 'updateReview'])->name('reviews.update');
    Route::delete('/opinie/{type}/{id}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
    
    Route::get('/wizyty', [AdminController::class, 'appointments'])->name('appointments');
    Route::get('/wizyty/{appointment}/edit', [AdminController::class, 'editAppointment'])->name('appointments.edit');
    Route::put('/wizyty/{appointment}', [AdminController::class, 'updateAppointment'])->name('appointments.update');
    Route::delete('/wizyty/{appointment}', [AdminController::class, 'destroyAppointment'])->name('appointments.destroy');
});

require __DIR__.'/auth.php';
