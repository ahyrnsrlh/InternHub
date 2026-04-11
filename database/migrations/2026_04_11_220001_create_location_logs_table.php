<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index(['user_id', 'logged_at']);
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_logs');
    }
};
