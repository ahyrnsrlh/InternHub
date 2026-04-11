<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedInteger('radius_meters')->default(100)->after('longitude');
            $table->string('status', 20)->default('active')->after('radius_meters');
            $table->index('status');
        });

        DB::table('locations')->whereNull('status')->update([
            'status' => 'active',
            'radius_meters' => 100,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['radius_meters', 'status']);
        });
    }
};
