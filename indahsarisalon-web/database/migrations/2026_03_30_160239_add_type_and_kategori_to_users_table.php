<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->nullable()->after('role'); 
            $table->string('kategori')->nullable()->after('type'); 
            // type: karyawan/pelanggan
            // kategori: stylist/biasa, nullable karena admin tidak punya kategori
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'kategori']);
        });
    }
};
