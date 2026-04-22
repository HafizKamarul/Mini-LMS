<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@mini-lms.test',
        ], [
            'name' => 'Admin User',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'student@mini-lms.test',
        ], [
            'name' => 'Student User',
            'role' => 'student',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
