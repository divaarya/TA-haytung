<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Iding',
            'email' => 'iding@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'admin'
        ]);
        User::create([
            'name' => 'Arkana',
            'email' => 'arkana@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'kandang'
        ]);
        User::create([
            'name' => 'Esta',
            'email' => 'esta@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'reseller'
        ]);
        User::create([
            'name' => 'Arya',
            'email' => 'arya@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'gudang'
        ]);
    }
}
