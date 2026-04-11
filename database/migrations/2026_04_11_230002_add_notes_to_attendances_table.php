<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'check_in_note')) {
                $table->text('check_in_note')->nullable()->after('check_in_time');
            }

            if (! Schema::hasColumn('attendances', 'check_out_note')) {
                $table->text('check_out_note')->nullable()->after('check_out_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'check_in_note')) {
                $table->dropColumn('check_in_note');
            }

            if (Schema::hasColumn('attendances', 'check_out_note')) {
                $table->dropColumn('check_out_note');
            }
        });
    }
};
