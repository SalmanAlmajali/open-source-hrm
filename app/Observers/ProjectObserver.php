<?php

namespace App\Observers;

use App\Models\CashReceivable;
use App\Models\Project;

class ProjectObserver
{
    /**
     * Auto-create a piutang (receivable) when a Project gets its first SPK number.
     * Q3 Detailed: record contract_value as receivable; actual cash recorded separately.
     */
    public function updated(Project $project): void
    {
        // Only trigger when spk_number is set for the first time (null → value)
        if (! $project->wasChanged('spk_number')) {
            return;
        }

        $oldSpk = $project->getOriginal('spk_number');
        $newSpk = $project->spk_number;

        if ($oldSpk !== null || blank($newSpk)) {
            // Already had an SPK, or SPK was cleared — do nothing
            return;
        }

        // Avoid duplicate receivables
        if ($project->receivable()->exists()) {
            return;
        }

        CashReceivable::create([
            'project_id'        => $project->id,
            'receivable_amount' => $project->contract_value ?? 0,
            'status'            => 'pending',
        ]);
    }
}
