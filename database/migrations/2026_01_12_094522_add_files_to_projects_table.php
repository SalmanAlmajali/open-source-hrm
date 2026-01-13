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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('offer_file_path')->nullable()->after('offer_number'); // File Penawaran
            $table->string('spk_file_path')->nullable()->after('spk_number'); // File SPK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['offer_file_path', 'spk_file_path']);
        });
    }
};
