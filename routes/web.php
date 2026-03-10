<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [EntryController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect('/dashboard');
    })->name('home');
    Route::post('/entries', [EntryController::class, 'store'])->name('entries.store');
    Route::patch('/entries/{entry}', [EntryController::class, 'update'])->name('entries.update');
    Route::get('/entries/{entry}', [EntryController::class, 'show'])->name('entries.show');
});

require __DIR__ . '/settings.php';
