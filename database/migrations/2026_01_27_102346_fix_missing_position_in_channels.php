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
        if (!Schema::hasColumn('channels', 'position')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->tinyInteger('position')->default(0)->after('name')->comment('Vị trí kênh');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('channels', 'position')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
    }
};
