<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_transaction_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['inflow', 'outflow']);
            $table->string('color', 7)->nullable(); // hex color e.g. #22c55e
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transaction_categories');
    }
};
