<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produkData = [
            [
                'nama' => 'Tas gendong',
                'harga_jual' => 150000,
                'stok' => 10,
            ],
            [
                'nama' => 'Tas punggung mini',
                'harga_jual' => 120000,
                'stok' => 15,
            ],
            [
                'nama' => 'Tas kanvas custom',
                'harga_jual' => 180000,
                'stok' => 8,
            ],
            [
                'nama' => 'Tas laptop',
                'harga_jual' => 250000,
                'stok' => 5,
            ],
            [
                'nama' => 'Totebag wanita',
                'harga_jual' => 140000,
                'stok' => 20,
            ],
        ];

        foreach ($produkData as $data) {
            Produk::firstOrCreate(
                ['nama' => $data['nama']],
                $data
            );
        }
    }
}
