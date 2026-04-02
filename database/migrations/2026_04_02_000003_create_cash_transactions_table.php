<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
            $table->enum('type', ['inflow', 'outflow', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->foreignUuid('category_id')->nullable()->constrained('cash_transaction_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable()->index();
            // Polymorphic source (Payroll, Project, etc.)
            $table->nullableMorphs('transactionable');
            $table->foreignUuid('transfer_to_account_id')->nullable()->constrained('cash_accounts')->nullOnDelete();
            // Q2: editable even when auto-generated (flag is just for display)
            $table->boolean('is_auto_generated')->default(false);
            $table->foreignUuid('recorded_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
