<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Q3 Detailed: Track project receivables separately from actual cash receipts
        Schema::create('cash_receivables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->decimal('receivable_amount', 15, 2);           // = project contract_value at SPK time
            $table->decimal('received_amount', 15, 2)->nullable();  // actual cash received
            $table->date('due_date')->nullable();
            $table->date('received_date')->nullable();
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            // Linked to CashTransaction created when payment is received
            $table->foreignUuid('cash_transaction_id')->nullable()->constrained('cash_transactions')->nullOnDelete();
            $table->foreignUuid('cash_account_id')->nullable()->constrained('cash_accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_receivables');
    }
};
