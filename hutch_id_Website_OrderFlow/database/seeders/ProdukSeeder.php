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
                'stok' => 111,
                'foto' => 'images/tas-gendong.jpg',
            ],
            [
                'nama' => 'Tas Kanvas Custom',
                'harga_jual' => 150000,
                'stok' => 70,
                'foto' => 'images/Tas canvas-custom.jpeg',
            ],
            [
                'nama' => 'Tas laptop',
                'harga_jual' => 250000,
                'stok' => 15,
                'foto' => 'images/Tas laptop.jpeg',
            ],
            [
                'nama' => 'Tas punggung mini',
                'harga_jual' => 120000,
                'stok' => 15,
                'foto' => 'images/Tas punggung mini.jpg',
            ],
            [
                'nama' => 'Totebag wanita',
                'harga_jual' => 140000,
                'stok' => 20,
                'foto' => 'images/Totebag wanita.jpeg',
            ],
        ];

        foreach ($produkData as $data) {
            Produk::updateOrCreate(
                ['nama' => $data['nama']],
                $data
            );
        }
    }
}
