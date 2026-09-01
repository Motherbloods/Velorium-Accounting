<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CoaAccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiscalPeriodController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TaxSettingController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\WarehouseController;
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

        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

        Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

        Route::get('/reports/general-ledger', [GeneralLedgerController::class, 'index'])->name('reports.general-ledger');
        Route::get('/reports/trial-balance', [TrialBalanceController::class, 'index'])->name('reports.trial-balance');

        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/in', [StockController::class, 'adjustIn'])->name('stock.in');
        Route::post('/stock/out', [StockController::class, 'adjustOut'])->name('stock.out');

        Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');
        Route::get('/receivables/create', [ReceivableController::class, 'create'])->name('receivables.create');
        Route::post('/receivables', [ReceivableController::class, 'store'])->name('receivables.store');
        Route::get('/receivables/aging', [ReceivableController::class, 'aging'])->name('receivables.aging');
        Route::post('/receivables/aging/record-allowance', [ReceivableController::class, 'recordAllowance'])->name('receivables.record-allowance');
        Route::get('/receivables/{receivable}', [ReceivableController::class, 'show'])->name('receivables.show');
        Route::post('/receivables/{receivable}/pay', [ReceivableController::class, 'pay'])->name('receivables.pay');

        Route::get('/payables', [PayableController::class, 'index'])->name('payables.index');
        Route::get('/payables/create-loan', [PayableController::class, 'createLoan'])->name('payables.create-loan');
        Route::post('/payables/store-loan', [PayableController::class, 'storeLoan'])->name('payables.store-loan');
        Route::get('/payables/{payable}', [PayableController::class, 'show'])->name('payables.show');
        Route::post('/payables/{payable}/pay', [PayableController::class, 'pay'])->name('payables.pay');

        Route::get('/tax/settings', [TaxSettingController::class, 'index'])->name('tax.settings');
        Route::post('/tax/settings', [TaxSettingController::class, 'store'])->name('tax.settings.store');

        Route::get('/tax/ppn', [TaxController::class, 'ppnIndex'])->name('tax.ppn');
        Route::post('/tax/ppn/setor', [TaxController::class, 'setorPpn'])->name('tax.ppn.setor');

        Route::get('/tax/pph', [TaxController::class, 'pphIndex'])->name('tax.pph');
        Route::post('/tax/pph/recognize', [TaxController::class, 'recognizePph'])->name('tax.pph.recognize');
        Route::post('/tax/pph/{pphFinalTransaction}/setor', [TaxController::class, 'setorPph'])->name('tax.pph.setor');
    });
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});