<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        // Hair Ritual
        $hairRitualId = DB::table('treatments')->insertGetId([
            'name' => 'Hair Ritual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $hairRituals = [
            ['name' => 'Hair Rejuvenation', 'duration_minutes' => 60, 'description' => 'Nutrisi & perbaikan rambut kering', 'price' => 120000],
            ['name' => 'Deep Conditioning', 'duration_minutes' => 45, 'description' => 'Perawatan rambut lembut & berkilau', 'price' => 100000],
            ['name' => 'Scalp Therapy', 'duration_minutes' => 30, 'description' => 'Perawatan kulit kepala & anti rontok', 'price' => 80000],
            ['name' => 'Hair Spa', 'duration_minutes' => 90, 'description' => 'Perawatan lengkap rambut & relaksasi', 'price' => 180000],
            ['name' => 'Keratin Treatment', 'duration_minutes' => 120, 'description' => 'Menghaluskan rambut & anti kusut', 'price' => 350000],
        ];

        foreach ($hairRituals as $ritual) {
            DB::table('treatment_details')->insert([
                'treatment_id' => $hairRitualId,
                'name' => $ritual['name'],
                'duration_minutes' => $ritual['duration_minutes'],
                'description' => $ritual['description'],
                'price' => $ritual['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hair Colouring + harga panjang rambut
        $hairColourId = DB::table('treatments')->insertGetId([
            'name' => 'Hair Colouring',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $hairColouring = [
            'Coloring Uban' => [150000,200000,250000,300000],
            'Hi-light / Balayage' => [200000,250000,300000,350000],
            'Non-Bleaching' => [180000,230000,280000,330000],
            'Bleaching Color' => [250000,300000,350000,400000],
            'Bleaching Only' => [200000,250000,300000,350000],
        ];

        $lengths = ['Short', 'Medium', 'Long', 'X-tra'];

        foreach ($hairColouring as $name => $prices) {
            $detailId = DB::table('treatment_details')->insertGetId([
                'treatment_id' => $hairColourId,
                'name' => $name,
                'duration_minutes' => null,
                'description' => null,
                'price' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($lengths as $index => $length) {
                DB::table('hair_length_prices')->insert([
                    'treatment_detail_id' => $detailId,
                    'hair_length' => $length,
                    'price' => $prices[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
}
