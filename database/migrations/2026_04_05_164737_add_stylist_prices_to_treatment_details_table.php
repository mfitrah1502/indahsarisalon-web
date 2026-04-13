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
        Schema::table('treatment_details', function (Blueprint $table) {
            $table->boolean('has_stylist_price')->default(false);
            $table->integer('price_senior')->nullable();
            $table->integer('price_junior')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatment_details', function (Blueprint $table) {
            $table->dropColumn(['has_stylist_price', 'price_senior', 'price_junior']);
        });
    }
};
