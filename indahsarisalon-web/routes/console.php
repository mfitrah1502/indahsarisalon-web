<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\Treatment;

Schedule::call(function () {
    // Menghapus User (karyawan, pelanggan, admin) yang sudah di-soft-delete > 30 hari lalu
    User::onlyTrashed()
        ->where('deleted_at', '<', now()->subDays(30))
        ->forceDelete();

    // Menghapus Treatment yang sudah di-soft-delete > 30 hari lalu
    Treatment::onlyTrashed()
        ->where('deleted_at', '<', now()->subDays(30))
        ->forceDelete();
})->daily();
