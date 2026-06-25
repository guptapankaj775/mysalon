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
        Schema::table('inventories', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('item_name');
            $table->decimal('cost', 10, 2)->nullable()->after('price');
            $table->decimal('special_price', 10, 2)->nullable()->after('cost');
            $table->string('tax_class')->nullable()->default('None')->after('special_price');
            $table->boolean('stock_status')->default(true)->after('quantity');
            $table->boolean('manage_stock')->default(true)->after('stock_status');
            $table->decimal('weight', 8, 2)->nullable()->after('unit_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'cost',
                'special_price',
                'tax_class',
                'stock_status',
                'manage_stock',
                'weight'
            ]);
        });
    }
};
