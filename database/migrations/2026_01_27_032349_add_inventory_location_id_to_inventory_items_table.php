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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Hapus kolom location lama (string) jika ingin diganti total, 
            // atau biarkan null jika ingin migrasi data dulu.
            // Disini kita asumsikan ganti total.
            $table->dropColumn('location');

            $table->foreignUuid('inventory_location_id')
                ->nullable()
                ->after('inventory_code_id')
                ->constrained('inventory_locations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_location_id']);
            $table->dropColumn('inventory_location_id');
            $table->string('location')->nullable();
        });
    }
};
