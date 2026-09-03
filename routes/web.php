<?php

use App\Http\Controllers\AdjustingEntryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CoaAccountController;
use App\Http\Controllers\ConsigneeController;
use App\Http\Controllers\ConsignmentReturnController;
use App\Http\Controllers\ConsignmentSalesReportController;
use App\Http\Controllers\ConsignmentShipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquityChangeController;
use App\Http\Controllers\FinancialNoteController;
use App\Http\Controllers\FinancialRatioController;
use App\Http\Controllers\FinancialReportExportController;
use App\Http\Controllers\FiscalPeriodController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\IncomeStatementController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\SaleController;
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

    Route::middleware('role:admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('role:admin,staff_konsinyasi')->group(function () {
        Route::get('/consignees', [ConsigneeController::class, 'index'])->name('consignees.index');
        Route::post('/consignees', [ConsigneeController::class, 'store'])->name('consignees.store');

        Route::get('/consignment/shipments', [ConsignmentShipmentController::class, 'index'])->name('consignment.shipments.index');
        Route::get('/consignment/shipments/create', [ConsignmentShipmentController::class, 'create'])->name('consignment.shipments.create');
        Route::post('/consignment/shipments', [ConsignmentShipmentController::class, 'store'])->name('consignment.shipments.store');
        Route::get('/consignment/shipments/{shipment}', [ConsignmentShipmentController::class, 'show'])->name('consignment.shipments.show');

        Route::get('/consignment/shipments/{shipment}/sales-report', [ConsignmentSalesReportController::class, 'create'])->name('consignment.sales-reports.create');
        Route::post('/consignment/shipments/{shipment}/sales-report', [ConsignmentSalesReportController::class, 'store'])->name('consignment.sales-reports.store');

        Route::get('/consignment/shipments/{shipment}/return', [ConsignmentReturnController::class, 'create'])->name('consignment.returns.create');
        Route::post('/consignment/shipments/{shipment}/return', [ConsignmentReturnController::class, 'store'])->name('consignment.returns.store');
    });

    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/cash', [CashTransactionController::class, 'index'])->name('cash.index');
        Route::get('/cash/create', [CashTransactionController::class, 'create'])->name('cash.create');
        Route::post('/cash', [CashTransactionController::class, 'store'])->name('cash.store');
    });

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

        Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::get('/bank-accounts/create', [BankAccountController::class, 'create'])->name('bank-accounts.create');
        Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');

        Route::get('/bank-reconciliations', [BankReconciliationController::class, 'index'])->name('bank-reconciliations.index');
        Route::get('/bank-reconciliations/create', [BankReconciliationController::class, 'create'])->name('bank-reconciliations.create');
        Route::post('/bank-reconciliations', [BankReconciliationController::class, 'store'])->name('bank-reconciliations.store');
        Route::get('/bank-reconciliations/{bankReconciliation}', [BankReconciliationController::class, 'show'])->name('bank-reconciliations.show');
        Route::post('/bank-reconciliations/{bankReconciliation}/items', [BankReconciliationController::class, 'addItem'])->name('bank-reconciliations.items.store');
        Route::post('/bank-reconciliations/items/{item}/post', [BankReconciliationController::class, 'postItem'])->name('bank-reconciliations.items.post');
        Route::post('/bank-reconciliations/{bankReconciliation}/complete', [BankReconciliationController::class, 'complete'])->name('bank-reconciliations.complete');

        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/sales/{sale}/return', [SaleController::class, 'createReturn'])->name('sales.return.create');
        Route::post('/sales/{sale}/return', [SaleController::class, 'storeReturn'])->name('sales.return.store');

        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/purchases/{purchase}/return', [PurchaseController::class, 'createReturn'])->name('purchases.return.create');
        Route::post('/purchases/{purchase}/return', [PurchaseController::class, 'storeReturn'])->name('purchases.return.store');

        Route::get('/fixed-assets', [FixedAssetController::class, 'index'])->name('fixed-assets.index');
        Route::get('/fixed-assets/create', [FixedAssetController::class, 'create'])->name('fixed-assets.create');
        Route::post('/fixed-assets', [FixedAssetController::class, 'store'])->name('fixed-assets.store');
        Route::get('/fixed-assets/{fixedAsset}', [FixedAssetController::class, 'show'])->name('fixed-assets.show');
        Route::post('/fixed-assets/{fixedAsset}/run-depreciation', [FixedAssetController::class, 'runDepreciation'])->name('fixed-assets.run-depreciation');
        Route::post('/fixed-assets/{fixedAsset}/dispose', [FixedAssetController::class, 'dispose'])->name('fixed-assets.dispose');

        Route::get('/adjusting-entries', [AdjustingEntryController::class, 'index'])->name('adjusting-entries.index');
        Route::get('/adjusting-entries/prepaid/create', [AdjustingEntryController::class, 'createPrepaid'])->name('adjusting-entries.prepaid.create');
        Route::post('/adjusting-entries/prepaid', [AdjustingEntryController::class, 'storePrepaid'])->name('adjusting-entries.prepaid.store');
        Route::post('/adjusting-entries/prepaid/{prepaidExpense}/run', [AdjustingEntryController::class, 'runPrepaid'])->name('adjusting-entries.prepaid.run');

        Route::get('/adjusting-entries/unearned/create', [AdjustingEntryController::class, 'createUnearned'])->name('adjusting-entries.unearned.create');
        Route::post('/adjusting-entries/unearned', [AdjustingEntryController::class, 'storeUnearned'])->name('adjusting-entries.unearned.store');
        Route::post('/adjusting-entries/unearned/{unearnedRevenue}/run', [AdjustingEntryController::class, 'runUnearned'])->name('adjusting-entries.unearned.run');

        Route::get('/adjusting-entries/accrued/create', [AdjustingEntryController::class, 'createAccrued'])->name('adjusting-entries.accrued.create');
        Route::post('/adjusting-entries/accrued/expense', [AdjustingEntryController::class, 'storeAccruedExpense'])->name('adjusting-entries.accrued.expense.store');
        Route::post('/adjusting-entries/accrued/revenue', [AdjustingEntryController::class, 'storeAccruedRevenue'])->name('adjusting-entries.accrued.revenue.store');

        Route::get('/reports/income-statement', [IncomeStatementController::class, 'index'])->name('reports.income-statement');
        Route::get('/reports/balance-sheet', [BalanceSheetController::class, 'index'])->name('reports.balance-sheet');

        Route::get('/reports/equity-change', [EquityChangeController::class, 'index'])->name('reports.equity-change');
        Route::get('/reports/cash-flow', [CashFlowController::class, 'index'])->name('reports.cash-flow');

        Route::get('/reports/financial-ratios', [FinancialRatioController::class, 'index'])->name('reports.financial-ratios');

        Route::get('/financial-notes', [FinancialNoteController::class, 'index'])->name('financial-notes.index');
        Route::post('/financial-notes', [FinancialNoteController::class, 'store'])->name('financial-notes.store');

        Route::get('/financial-report-export', [FinancialReportExportController::class, 'index'])->name('financial-report-export.index');
        Route::get('/financial-report-export/{fiscalPeriod}/pdf', [FinancialReportExportController::class, 'pdf'])->name('financial-report-export.pdf');
        Route::get('/financial-report-export/{fiscalPeriod}/excel', [FinancialReportExportController::class, 'excel'])->name('financial-report-export.excel');
    });
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});