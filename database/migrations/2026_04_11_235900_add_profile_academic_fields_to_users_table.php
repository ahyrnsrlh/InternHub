<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'npm')) {
                $table->string('npm', 50)->nullable()->after('placement');
            }

            if (! Schema::hasColumn('users', 'institution_name')) {
                $table->string('institution_name')->nullable()->after('npm');
            }

            if (! Schema::hasColumn('users', 'academic_supervisor')) {
                $table->string('academic_supervisor')->nullable()->after('institution_name');
            }

            if (! Schema::hasColumn('users', 'field_supervisor')) {
                $table->string('field_supervisor')->nullable()->after('academic_supervisor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                Schema::hasColumn('users', 'field_supervisor') ? 'field_supervisor' : null,
                Schema::hasColumn('users', 'academic_supervisor') ? 'academic_supervisor' : null,
                Schema::hasColumn('users', 'institution_name') ? 'institution_name' : null,
                Schema::hasColumn('users', 'npm') ? 'npm' : null,
            ]);

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
