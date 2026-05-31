<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RandomTreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil ID kategori yang tersedia di database
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        // Jika belum ada kategori sama sekali, buat satu kategori default
        if (empty($categoryIds)) {
            $categoryIds[] = DB::table('categories')->insertGetId([
                'name' => 'General Treatment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $treatmentNames = [
            'Smoothing Keratin',
            'Creambath Ginseng',
            'Hair Extension Premium',
            'Eyelash Extension',
            'Creambath Lidah Buaya',
            'Scrub Tubuh Rempah'
        ];

        // Ambil 5 data treatment acak
        $selectedNames = $faker->randomElements($treatmentNames, 5);

        foreach ($selectedNames as $name) {
            $treatmentId = DB::table('treatments')->insertGetId([
                'name' => $name,
                'category_id' => $faker->randomElement($categoryIds),
                'is_promo' => $faker->boolean(20), // Peluang 20% menjadi promo
                'is_active' => true,
                'allow_multi_select' => $faker->boolean(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat 1 sampai 3 detail treatment random untuk setiap treatment utama
            $detailCount = rand(1, 3);
            for ($i = 1; $i <= $detailCount; $i++) {
                $detailName = $detailCount === 1 ? 'Regular' : ($i === 1 ? 'Basic' : ($i === 2 ? 'Medium' : 'Premium'));
                DB::table('treatment_details')->insert([
                    'treatment_id' => $treatmentId,
                    'name' => $detailName,
                    'duration' => $faker->randomElement([30, 45, 60, 90, 120]),
                    'price' => $faker->randomElement([50000, 75000, 100000, 120000, 150000, 200000, 250000, 300000]),
                    'description' => 'Perawatan ' . strtolower($name) . ' untuk hasil yang maksimal dan memuaskan.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
