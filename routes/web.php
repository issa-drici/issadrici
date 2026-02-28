<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/booking/{token}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking/{token}', [BookingController::class, 'store'])->name('booking.store');
