<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'              => 'System Admin',
            'username'          => 'admin',
            'email'             => 'admin@lara-axios.sar',
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);

        User::factory(100)->create();
    }
}
