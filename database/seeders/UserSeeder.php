<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@library.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '081234567890',
            'address'  => 'Jl. Pendidikan No. 1, Samarinda',
        ]);

        // Members
        $members = [
            ['name' => 'Ahmad Saifuddin Dzaki',   'email' => '2411102441306@umkt.ac.id'],
            ['name' => 'Fara Aulia Ananda Gunawan',      'email' => '2411102441076@umkt.ac.id'],
            ['name' => 'Muhammad Naufal Putra', 'email' => '2411102441246@umkt.ac.id'],
            ['name' => 'Kurnia Dwi Agustin',   'email' => '24111022441284@umkt.ac.id'],
            ['name' => 'Ariani Sapto',  'email' => '2411102441262@umkt.ac.id'],
            ['name' => 'Satriani',     'email' => '2411102441122@umkt.ac.id'],
        ];

        foreach ($members as $member) {
            User::create([
                'name'     => $member['name'],
                'email'    => $member['email'],
                'password' => Hash::make('password'),
                'role'     => 'member',
            ]);
        }

        // Akun member default untuk testing
        User::create([
            'name'     => 'Member',
            'email'    => 'member@library.id',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);
    }
}