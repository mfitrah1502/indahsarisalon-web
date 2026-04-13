<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropColumn('category'); // hapus kolom yang konflik
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->string('category')->nullable(); // rollback kalau perlu
        });
    }
};
