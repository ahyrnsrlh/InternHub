<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'location_tracking_enabled')) {
                $table->boolean('location_tracking_enabled')->default(false)->after('face_registered');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'location_tracking_enabled')) {
                $table->dropColumn('location_tracking_enabled');
            }
        });
    }
};
