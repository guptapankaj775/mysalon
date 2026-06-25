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
            $table->dropColumn(['brand', 'category']);
            $table->foreignId('brand_id')->nullable()->after('item_name')->constrained('brands')->nullOnDelete();
            $table->foreignId('inventory_category_id')->nullable()->after('brand_id')->constrained('inventory_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['inventories_brand_id_foreign']);
            $table->dropForeign(['inventories_inventory_category_id_foreign']);
            $table->dropColumn(['brand_id', 'inventory_category_id']);
            $table->string('brand')->nullable()->after('item_name');
            $table->string('category')->nullable()->after('brand');
        });
    }
};
