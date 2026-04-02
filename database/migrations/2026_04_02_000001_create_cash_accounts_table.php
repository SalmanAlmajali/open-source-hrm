<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['cash', 'bank', 'e-wallet'])->default('cash');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->boolean('is_active')->default(true);
            // Q1B: default account flags for auto-generated HRM transactions
            $table->boolean('default_for_payroll')->default(false);
            $table->boolean('default_for_income')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};
