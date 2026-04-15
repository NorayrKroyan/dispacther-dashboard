<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_driver_current_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('id_join');
            $table->string('current_status', 40);
            $table->timestamp('state_started_at')->nullable();
            $table->timestamp('predicted_return_at')->nullable();
            $table->string('last_event', 80)->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->string('updated_by_name', 120)->nullable();
            $table->timestamps();

            $table->unique(['id_driver', 'id_join'], 'dispatch_current_state_driver_join_uq');
            $table->index(['id_join', 'current_status'], 'dispatch_current_state_join_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_driver_current_states');
    }
};
