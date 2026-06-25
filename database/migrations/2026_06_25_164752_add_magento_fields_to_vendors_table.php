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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('website')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('website');
            $table->string('payment_terms')->nullable()->after('tax_number');
            $table->string('bank_name')->nullable()->after('payment_terms');
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->string('bank_code')->nullable()->after('bank_account');
            $table->string('logo_path')->nullable()->after('bank_code');
            $table->text('description')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'tax_number',
                'payment_terms',
                'bank_name',
                'bank_account',
                'bank_code',
                'logo_path',
                'description',
            ]);
        });
    }
};
