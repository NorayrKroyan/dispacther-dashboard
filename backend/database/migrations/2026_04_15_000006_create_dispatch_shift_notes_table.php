<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_shift_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_join');
            $table->string('shift_key', 30);
            $table->unsignedBigInteger('started_by_user_id')->nullable();
            $table->string('started_by_name', 120);
            $table->unsignedBigInteger('ended_by_user_id')->nullable();
            $table->string('ended_by_name', 120)->nullable();
            $table->longText('note_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['id_join', 'is_active'], 'dispatch_shift_notes_join_active_idx');
            $table->index(['id_join', 'shift_key'], 'dispatch_shift_notes_join_shift_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_shift_notes');
    }
};
