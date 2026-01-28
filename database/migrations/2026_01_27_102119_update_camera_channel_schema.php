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
        Schema::table('cameras', function (Blueprint $table) {
            $table->string('device_model')->nullable()->after('device_id');
            $table->integer('total_channels')->default(0)->after('channel_id');
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->boolean('is_activated')->default(false)->after('status')->comment('Đã được kích hoạt license');
            $table->boolean('has_stream')->default(false)->after('is_activated')->comment('Có luồng stream');
            $table->string('live_token')->nullable()->after('has_stream');
            $table->string('live_url_hls')->nullable()->after('live_token');
            $table->string('live_url_https')->nullable()->after('live_url_hls');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cameras', function (Blueprint $table) {
            $table->dropColumn(['device_model', 'total_channels']);
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn(['is_activated', 'has_stream', 'live_token', 'live_url_hls', 'live_url_https']);
        });
    }
};
