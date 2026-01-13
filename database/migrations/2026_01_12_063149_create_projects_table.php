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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Identitas Proyek
            $table->string('name'); // nama_projek
            $table->string('offer_number')->nullable(); // surat_penawaran
            
            // Status SPK (Surat Perintah Kerja)
            $table->string('spk_number')->nullable(); // no_spk
            $table->date('spk_date')->nullable(); // tanggal_spk
            $table->date('plan_date')->default(now()); // tanggal_rencana (untuk sorting jika belum ada SPK)

            // Keuangan (Financials)
            $table->decimal('contract_value', 15, 2)->default(0); // nilai_pekerjaan
            $table->decimal('tax_base', 15, 2)->default(0); // dpp (Dasar Pengenaan Pajak)
            $table->decimal('vat', 15, 2)->default(0); // ppn
            $table->integer('vat_rate')->default(11); // ppn_persen (default 11%)
            
            $table->decimal('income_tax', 15, 2)->default(0); // pph
            $table->integer('income_tax_rate')->default(0); // pph_persen
            
            $table->decimal('flag_fee', 15, 2)->default(0); // feebendera
            $table->integer('flag_fee_rate')->default(0); // feebendera_persen

            $table->decimal('net_income', 15, 2)->default(0); // jumlah_pemasukan
            $table->decimal('profit', 15, 2)->nullable(); // profit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
