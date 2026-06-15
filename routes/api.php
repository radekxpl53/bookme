<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/businesses', [\App\Http\Controllers\Api\BusinessApiController::class, 'index']);
Route::get('/businesses/{id}', [\App\Http\Controllers\Api\BusinessApiController::class, 'show']);
