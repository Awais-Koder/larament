<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Register;

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // ✅ This updates email_verified_at

    //return redirect('/admin/login')
    return redirect()->route('filament.admin.auth.login')
    ->with('success', 'Email verified! You can now log in.');
})->middleware(['signed'])->name('verification.verify');

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
// Route::get('/', function () {
//     return view('welcome');
// });
//Route::redirect('/register', '/admin/register')->name('register');

// Route::middleware('auth')->group(function () {
//     Route::get('/email/verify', function () {
//         return view('auth.verify'); // You can make this view simple
//     })->name('verification.notice');

//     Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//         $request->fulfill(); // ✅ This updates email_verified_at

//         return redirect('/admin/login')->with('success', 'Email verified! You can now log in.');
//     })->middleware(['signed'])->name('verification.verify');
// });
// Route::middleware(['web'])->group(function () {
//     Filament::routes();
// });
// Route::get('/admin/register', Register::class)->name('filament.admin.auth.register');

//Route::redirect('/login', '/admin/login')->name('login');

// Route::get('/admin/email-verification/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill(); // This updates email_verified_at 

//     return redirect('/admin'); // or redirect()->route('filament.admin.pages.dashboard')
// })->middleware(['signed'])->name('filament.admin.auth.email-verification.verify');


// Route::view('/admin/email/verified', 'filament.admin.pages.auth.verify')
//     ->middleware(['auth'])
//     ->name('email.verified');
