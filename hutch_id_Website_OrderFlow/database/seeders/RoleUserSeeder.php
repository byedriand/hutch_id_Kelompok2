<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrator
        User::firstOrCreate(
            ['email' => 'admin@hutch.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password123'),
                'role' => 'administrator',
                'email_verified_at' => now(),
            ]
        );

        // Pemilik UMKM
        User::firstOrCreate(
            ['email' => 'pemilik@hutch.id'],
            [
                'name' => 'Pemilik UMKM',
                'password' => bcrypt('password123'),
                'role' => 'pemilik_umkm',
                'email_verified_at' => now(),
            ]
        );

        // Staf Penjualan
        User::firstOrCreate(
            ['email' => 'staf@hutch.id'],
            [
                'name' => 'Staf Penjualan',
                'password' => bcrypt('password123'),
                'role' => 'staf_penjualan',
                'email_verified_at' => now(),
            ]
        );

        // Operator Gudang
        User::firstOrCreate(
            ['email' => 'operator@hutch.id'],
            [
                'name' => 'Operator Gudang',
                'password' => bcrypt('password123'),
                'role' => 'operator_gudang',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Role-based users created successfully!');
    }
}
