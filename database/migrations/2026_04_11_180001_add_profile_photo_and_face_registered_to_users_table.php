<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('mentor_id');
            }

            if (! Schema::hasColumn('users', 'face_registered')) {
                $table->boolean('face_registered')->default(false)->after('face_descriptor');
            }
        });

        if (Schema::hasColumn('users', 'face_registered') && Schema::hasColumn('users', 'face_descriptor')) {
            DB::table('users')
                ->whereNotNull('face_descriptor')
                ->update(['face_registered' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'face_registered')) {
                $table->dropColumn('face_registered');
            }

            if (Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_photo');
            }
        });
    }
};
