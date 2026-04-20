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
        Schema::create('bottlenecks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('reported_by')->constrained('employees')->cascadeOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['open', 'acknowledge', 'resolved'])->default('open');
            $table->foreignUuid('acknowledged_by')->nullable()->constrained('employees');
            $table->timestamp('acknowledged_at')->nullable();

            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('employees');

            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bottlenecks');
    }
};
