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
        Schema::create('project_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('hours_spent')->nullable();
            $table->date('progress_date');
            $table->foreignUuid('acknowledged_by')->nullable()->constrained('employees');
            $table->timestamp('acknowledged_at')->nullable();

            // Audit trail for soft deletes
            $table->foreignUuid('deleted_by')->nullable()->constrained('employees');

            $table->timestamps();

            $table->softDeletes();

            $table->index(['project_id', 'progress_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress');
    }
};
