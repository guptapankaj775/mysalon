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
        Schema::table('specialists', function (Blueprint $table) {
            $table->string('mobile_no')->nullable()->after('name');
            $table->string('email')->nullable()->after('mobile_no');
            $table->string('job_category')->nullable()->after('email');
            $table->text('home_address')->nullable()->after('job_category');
            $table->string('religion')->nullable()->after('home_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn(['mobile_no', 'email', 'job_category', 'home_address', 'religion']);
        });
    }
};
