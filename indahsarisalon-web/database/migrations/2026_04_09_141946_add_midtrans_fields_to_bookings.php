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
        Schema::table('bookings', function (Blueprint $blueprint) {
            $blueprint->string('payment_method')->default('cash')->after('payment_status');
            $blueprint->string('snap_token')->nullable()->after('payment_method');
            $blueprint->string('midtrans_id')->nullable()->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['payment_method', 'snap_token', 'midtrans_id']);
        });
    }
};
