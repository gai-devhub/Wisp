<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'gaicorporation.official@gmail.com'],
            [
                'name' => 'GAI Management',
                'username' => 'gaimanagement',
                'password' => \Illuminate\Support\Facades\Hash::make('gai@wispadmin'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
