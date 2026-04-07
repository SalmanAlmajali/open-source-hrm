<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/portal');
});

// Cashflow Report Exports — guarded by Filament's employee auth
Route::middleware(['role:karyawan'])->group(function () {
    Route::get('/cashflow/report/pdf', [\App\Http\Controllers\CashFlowReportController::class, 'exportPdf'])
        ->name('cashflow.report.pdf');

    Route::get('/cashflow/report/excel', [\App\Http\Controllers\CashFlowReportController::class, 'exportExcel'])
        ->name('cashflow.report.excel');
});
