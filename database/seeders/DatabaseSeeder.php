<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff@kantor.com'],
            [
                'name' => 'Staf Administrasi',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pimpinan@kantor.com'],
            [
                'name' => 'Kepala Bagian',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ]
        );
    }
}
