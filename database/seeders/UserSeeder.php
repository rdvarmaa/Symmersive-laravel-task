<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Seeding User');
        User::create([
            'name' => 'user',
            'email' => 'user@test.in',
            'password' => Hash::make('password'),
        ]);
    }
}
