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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('gst_number')->nullable()->after('phone');
            $table->boolean('has_no_gst')->default(false)->after('gst_number');
            $table->string('billing_name')->nullable()->after('has_no_gst');
            $table->string('trade_name')->nullable()->after('billing_name');
            $table->text('billing_address')->nullable()->after('trade_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'phone',
                'gst_number',
                'has_no_gst',
                'billing_name',
                'trade_name',
                'billing_address',
            ]);
        });
    }
};
