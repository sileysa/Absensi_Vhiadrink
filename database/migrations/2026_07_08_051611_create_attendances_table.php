<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stand_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->timestamp('attended_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stand_id', 'attended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
