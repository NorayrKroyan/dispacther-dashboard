<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_driver_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('id_join');
            $table->text('note_text')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->string('created_by_name', 120)->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->string('updated_by_name', 120)->nullable();
            $table->timestamps();

            $table->unique(['id_driver', 'id_join'], 'dispatch_driver_notes_driver_join_uq');
            $table->index(['id_join'], 'dispatch_driver_notes_join_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_driver_notes');
    }
};
