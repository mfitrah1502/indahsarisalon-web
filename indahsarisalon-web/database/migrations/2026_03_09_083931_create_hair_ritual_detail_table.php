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
       
       Schema::create('hair_ritual_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade'); // treatment_id Hair Ritual
    $table->string('name');
    $table->integer('duration_minutes');
    $table->text('description')->nullable();
    $table->decimal('price', 12, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hair_ritual_details');
    }
};
