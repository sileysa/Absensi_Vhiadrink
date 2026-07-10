<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'owner@vhiadrink.com'],
            [
                'name' => 'Owner Vhiadrink',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '081234567890',
            ]
        );

        $employees = [
            ['name' => 'Budi Santoso', 'email' => 'budi@vhiadrink.com', 'phone' => '081111111111'],
            ['name' => 'Siti Aminah', 'email' => 'siti@vhiadrink.com', 'phone' => '082222222222'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky@vhiadrink.com', 'phone' => '083333333333'],
        ];

        foreach ($employees as $employee) {
            User::query()->updateOrCreate(
                ['email' => $employee['email']],
                [
                    'name' => $employee['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Employee,
                    'phone' => $employee['phone'],
                ]
            );
        }

        $stands = [
            ['name' => 'Stand Malioboro', 'region' => 'Yogyakarta', 'address' => 'Jl. Malioboro No. 12'],
            ['name' => 'Stand Monas', 'region' => 'Jakarta', 'address' => 'Kawasan Monas, Jakarta Pusat'],
            ['name' => 'Stand Dago', 'region' => 'Bandung', 'address' => 'Jl. Dago No. 45'],
            ['name' => 'Stand Pantai Kuta', 'region' => 'Bali', 'address' => 'Jl. Pantai Kuta, Badung'],
            ['name' => 'Stand Tunjungan', 'region' => 'Surabaya', 'address' => 'Jl. Tunjungan No. 8'],
            ['name' => 'Stand Simpang Lima', 'region' => 'Semarang', 'address' => 'Simpang Lima, Semarang'],
        ];

        foreach ($stands as $stand) {
            Stand::query()->updateOrCreate(
                ['name' => $stand['name']],
                [
                    'region' => $stand['region'],
                    'address' => $stand['address'],
                    'is_active' => true,
                ]
            );
        }
    }
}
