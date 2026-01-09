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
        Schema::table('showrooms', function (Blueprint $table) {
            if(Schema::hasColumn('showrooms', 'hotlines')) {
                $table->dropColumn('hotlines');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if(!Schema::hasColumn('departments', 'hotlines')) {
                $table->json('hotlines')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            if(!Schema::hasColumn('showrooms', 'hotlines')) {
                $table->json('hotlines');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if(Schema::hasColumn('departments', 'hotlines')) {
                $table->dropColumn('hotlines');
            }
        });
    }
};
