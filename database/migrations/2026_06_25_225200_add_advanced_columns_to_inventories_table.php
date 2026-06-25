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
            $table->string('brand')->nullable()->after('item_name');
            $table->string('category')->nullable()->after('brand');
            $table->string('division')->nullable()->after('category');
            $table->string('hsn_code')->nullable()->after('sku');
            $table->decimal('gst_percent', 5, 2)->nullable()->after('tax_class');
            $table->decimal('mrp', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('mrp');
            $table->decimal('purchase_discount_percent', 5, 2)->nullable()->after('cost');
            $table->decimal('additional_discount_percent', 5, 2)->nullable()->after('purchase_discount_percent');
            $table->decimal('additional_discount_amount', 10, 2)->nullable()->after('additional_discount_percent');
            $table->decimal('taxable_amount', 10, 2)->nullable()->after('additional_discount_amount');
            $table->decimal('gst_input', 10, 2)->nullable()->after('gst_percent');
            $table->decimal('gst_output', 10, 2)->nullable()->after('gst_input');
            $table->string('size')->nullable()->after('unit_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'category',
                'division',
                'hsn_code',
                'gst_percent',
                'mrp',
                'discount_percent',
                'purchase_discount_percent',
                'additional_discount_percent',
                'additional_discount_amount',
                'taxable_amount',
                'gst_input',
                'gst_output',
                'size'
            ]);
        });
    }
};
