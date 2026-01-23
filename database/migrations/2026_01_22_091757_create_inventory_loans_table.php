<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignUuid('item_id')->nullable()->constrained('inventory_loans')->nullOnDelete();
            $table->integer('amount')->default(0);
            $table->date('loan_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'borrowed', 'returned'])
                ->default('pending')
                ->comment('pending: waiting for approval, approved: loan approved, returned: item returned, rejected: loan rejected');
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('return_picture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_loans');
    }
};
