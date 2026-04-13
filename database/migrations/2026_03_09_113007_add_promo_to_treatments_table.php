<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->boolean('is_promo')->default(false);
            $table->string('promo_type')->nullable(); // percent/fixed
            $table->decimal('promo_value', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropColumn(['is_promo', 'promo_type', 'promo_value']);
        });
    }
};
