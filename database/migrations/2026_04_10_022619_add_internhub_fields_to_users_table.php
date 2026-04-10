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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('intern')->after('email');
            $table->string('title')->nullable()->after('role');
            $table->string('department')->nullable()->after('title');
            $table->string('placement')->nullable()->after('department');
            $table->string('status')->default('active')->after('placement');
            $table->foreignId('mentor_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mentor_id');
            $table->dropColumn(['role', 'title', 'department', 'placement', 'status']);
        });
    }
};
