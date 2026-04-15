<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_job_driver_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_join');
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('assigned_by_user_id')->nullable();
            $table->string('assigned_by_name', 120)->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['id_join', 'id_driver'], 'dispatch_assignments_join_driver_uq');
            $table->index(['id_join', 'is_active'], 'dispatch_assignments_join_active_idx');
            $table->index(['id_driver', 'is_active'], 'dispatch_assignments_driver_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_job_driver_assignments');
    }
};
