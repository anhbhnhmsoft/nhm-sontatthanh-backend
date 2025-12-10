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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('discount_percent')->default(0)->nullable();
            $table->decimal('sell_price', 15, 2)->default(0)->comment('Giá bán');
            $table->decimal('price_discount', 15, 2)->default(0)->comment('Phần sau chiết khấu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
            $table->dropColumn('sell_price');
            $table->dropColumn('price_discount');
        });
    }
};
