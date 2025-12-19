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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->comment('Kênh video của camera');
            $table->foreignId('camera_id')->constrained('cameras')->cascadeOnDelete();
            $table->tinyInteger('status')->comment('Trạng thái');
            $table->string('name')->comment('Tên kênh');
            $table->tinyInteger('position')->comment('Vị trí kênh');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('cameras', function (Blueprint $table) {
            $table->dropColumn('device_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
        Schema::table('cameras', function (Blueprint $table) {
            $table->string('device_model')->nullable()->comment('Model camera');
        });
    }
};
