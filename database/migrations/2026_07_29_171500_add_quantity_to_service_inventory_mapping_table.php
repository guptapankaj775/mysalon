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
        if (!Schema::hasColumn('service_inventory_mapping', 'quantity')) {
            Schema::table('service_inventory_mapping', function (Blueprint $table) {
                $table->decimal('quantity', 10, 2)->default(1.00)->after('inventory_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('service_inventory_mapping', 'quantity')) {
            Schema::table('service_inventory_mapping', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
