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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['id','notifiable_type']);
            $table->id()->first();
            $table->foreignId('user_id')
                ->after('type')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('title')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('title');
        });
        // Sử dụng SQL thuần để ép kiểu cho PostgreSQL
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE JSON USING data::json');
        DB::statement('ALTER TABLE notifications ALTER COLUMN data DROP NOT NULL');
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('expo_push_token')->unique();
            $table->string('device_id')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE TEXT USING data::text');
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['id', 'user_id', 'title', 'description']);
            $table->uuid('id')->primary()->first();
            $table->morphs('notifiable');
        });

        Schema::dropIfExists('user_devices');
    }
};
