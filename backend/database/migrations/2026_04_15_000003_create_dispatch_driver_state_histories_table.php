<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_driver_state_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('id_join')->nullable();
            $table->string('status', 40);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('predicted_return_at')->nullable();
            $table->string('last_event', 80)->nullable();
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('changed_by_name', 120)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['id_driver', 'id_join'], 'dispatch_state_histories_driver_join_idx');
            $table->index(['id_driver', 'started_at'], 'dispatch_state_histories_driver_started_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_driver_state_histories');
    }
};
