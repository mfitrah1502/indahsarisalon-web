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
        Schema::create('hair_length_prices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('treatment_detail_id')->constrained('treatment_details')->onDelete('cascade');
    $table->string('hair_length'); // short, medium, long, x-tra
    $table->decimal('price', 12, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hair_length_prices');
    }
};
