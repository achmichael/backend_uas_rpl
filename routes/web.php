<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return response()
        ->view('welcome')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
});


Route::middleware('web')->group(function () {
   
Route::get('/home', function () {
    return view('home');
})->middleware(['check']);

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::get('/test', function(Request $request) {
    return $request->user();
})->middleware(['web', 'guest']);

Route::post('/email/verification-notification', function (Request $request){
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
});



Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Not Found',
    ], 404);
});