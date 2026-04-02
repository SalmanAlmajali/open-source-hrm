<?php

namespace App\Observers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\CashTransactionCategory;
use App\Models\Payroll;

class PayrollObserver
{
    /**
     * Auto-create a cashflow outflow when a payroll is marked as completed.
     */
    public function updated(Payroll $payroll): void
    {
        // Only trigger when status transitions TO 'completed'
        if (! $payroll->wasChanged('status') || $payroll->status !== 'completed') {
            return;
        }

        $account = CashAccount::getDefaultForPayroll();
        if (! $account) {
            // No default payroll account configured — skip silently
            return;
        }

        $category = CashTransactionCategory::findByName('Gaji Karyawan');

        CashTransaction::create([
            'cash_account_id'      => $account->id,
            'type'                 => 'outflow',
            'amount'               => $payroll->net_pay,
            'transaction_date'     => $payroll->pay_date ?? now(),
            'category_id'          => $category?->id,
            'description'          => "Gaji {$payroll->employee->name} — Periode {$payroll->period}",
            'reference_number'     => 'PAY-' . $payroll->id,
            'transactionable_type' => Payroll::class,
            'transactionable_id'   => $payroll->id,
            'is_auto_generated'    => true,
        ]);
    }
}
