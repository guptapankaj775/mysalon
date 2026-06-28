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
        // 1. Modify vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('group');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('status')->constrained('groups')->nullOnDelete();
        });

        // 2. Modify sales_invoices table
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('group');
        });
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('status')->constrained('groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse vendors
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('group')->default('Creditor')->after('status');
        });

        // Reverse sales_invoices
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->string('group')->default('Debtor')->after('status');
        });
    }
};
