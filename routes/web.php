<?php

use App\Http\Controllers\LetterDownloadController;
use App\Livewire\DashboardPage;
use App\Livewire\LetterIndex;
use App\Livewire\LoginPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', LoginPage::class)
    ->middleware('guest')
    ->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');
    Route::get('/letters', LetterIndex::class)->name('letters.index');
    Route::get('/letters/{letter}/download', LetterDownloadController::class)->name('letters.download');
});
