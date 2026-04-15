<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_driver_tracking_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('id_join');
            $table->string('last_event', 80)->nullable();
            $table->decimal('miles_to_job', 10, 2)->nullable();
            $table->integer('eta_to_deliver_minutes')->nullable();
            $table->decimal('gps_lat', 11, 7)->nullable();
            $table->decimal('gps_lng', 11, 7)->nullable();
            $table->timestamp('gps_recorded_at')->nullable();
            $table->string('source_table', 120)->nullable();
            $table->string('source_id', 120)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['id_driver', 'id_join'], 'dispatch_tracking_driver_join_uq');
            $table->index(['id_join'], 'dispatch_tracking_join_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_driver_tracking_snapshots');
    }
};
