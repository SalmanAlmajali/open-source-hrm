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
        // 1. Tabel Surat Masuk
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas Surat
            $table->string('reference_number')->nullable(); // Nomor Surat dari Pengirim

            $table->date('letter_date'); // Tanggal yang tertera di surat
            $table->date('received_date')->default(now()); // Tanggal diterima

            // Pengirim & Tujuan
            $table->string('sender'); // Dari instansi mana
            $table->string('recipient')->nullable(); // Ditujukan kepada siapa

            // Isi
            $table->string('subject'); // Perihal
            $table->text('description')->nullable(); // Ringkasan isi

            // File & Klasifikasi
            $table->string('file_path')->nullable(); // Scan surat

            // Status Disposisi
            $table->string('status')->default('received'); // received, processed, archived

            $table->timestamps();
        });

        // 2. Tabel Surat Keluar
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('reference_number')->unique(); // Nomor Surat Keluar (Wajib Unik)
            $table->date('letter_date'); // Tanggal Surat

            $table->string('recipient'); // Kepada instansi mana
            $table->string('subject'); // Perihal
            $table->text('description')->nullable();

            // Penandatangan
            $table->foreignUuid('signed_by')->nullable()->constrained('employees')->nullOnDelete();

            $table->string('file_path')->nullable(); // File arsip surat keluar
            $table->string('status')->default('draft'); // draft, sent, archived

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_letters');
        Schema::dropIfExists('incoming_letters');
    }
};
