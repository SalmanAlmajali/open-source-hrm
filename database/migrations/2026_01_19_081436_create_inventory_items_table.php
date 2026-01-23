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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas Barang
            $table->foreignUuid('inventory_code_id')->nullable()->constrained('inventory_codes')->nullOnDelete();
            $table->string('unique_id')->unique(); // Kode Barang
            $table->unique(['inventory_code_id', 'unique_id']);
            $table->string('name'); // Nama Barang
            $table->string('brand')->nullable(); // Merk

            // Stok & Kondisi
            $table->integer('stock')->default(1); // Jumlah Stok
            $table->string('unit')->default('pcs'); // Satuan (pcs, rim, box)
            $table->string('condition')->default('good'); // good, broken, repair

            // Pengelompokan
            $table->string('type'); // 'asset' (Inventaris) atau 'consumable' (Habis Pakai)
            $table->string('category')->nullable(); // Elektronik, ATK, Kendaraan, dll

            // Info Pembelian
            $table->date('purchase_date')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('location')->nullable(); // Lokasi penyimpanan
            
            $table->string('image_path')->nullable(); // Foto barang
            $table->text('description')->nullable();

            // QR code path
            $table->string('qr_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
