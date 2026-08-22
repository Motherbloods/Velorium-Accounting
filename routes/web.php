<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CoaAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiscalPeriodController;
use App\Http\Controllers\JournalEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,akuntan')->group(function () {
        Route::get('/coa', [CoaAccountController::class, 'index'])->name('coa.index');
        Route::get('/coa/create', [CoaAccountController::class, 'create'])->name('coa.create');
        Route::post('/coa', [CoaAccountController::class, 'store'])->name('coa.store');
        Route::get('/coa/{coa}/edit', [CoaAccountController::class, 'edit'])->name('coa.edit');
        Route::put('/coa/{coa}', [CoaAccountController::class, 'update'])->name('coa.update');

        Route::get('/fiscal-periods', [FiscalPeriodController::class, 'index'])->name('fiscal-periods.index');
        Route::post('/fiscal-periods', [FiscalPeriodController::class, 'store'])->name('fiscal-periods.store');
        Route::post('/fiscal-periods/{fiscalPeriod}/close', [FiscalPeriodController::class, 'close'])->name('fiscal-periods.close');

        Route::get('/journal', [JournalEntryController::class, 'index'])->name('journal.index');
        Route::get('/journal/create', [JournalEntryController::class, 'create'])->name('journal.create');
        Route::post('/journal', [JournalEntryController::class, 'store'])->name('journal.store');
        Route::get('/journal/{journal}', [JournalEntryController::class, 'show'])->name('journal.show');
        Route::post('/journal/{journal}/submit', [JournalEntryController::class, 'submit'])->name('journal.submit');
        Route::post('/journal/{journal}/approve', [JournalEntryController::class, 'approve'])->name('journal.approve');
        Route::post('/journal/{journal}/reject', [JournalEntryController::class, 'reject'])->name('journal.reject');
        Route::post('/journal/{journal}/back-to-draft', [JournalEntryController::class, 'backToDraft'])->name('journal.back-to-draft');
        Route::post('/journal/{journal}/post', [JournalEntryController::class, 'post'])->name('journal.post');
    });
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});