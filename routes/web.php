<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\BusinessController;

Route::middleware(['auth', 'owner'])->prefix('biznes')->name('biznes.')->group(function () {
    Route::resource('lokale', BusinessController::class);

});
