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
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'intern',
                'title' => 'Intern',
                'department' => 'Strategic Architecture',
                'placement' => 'Product Design',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->call(InternHubSeeder::class);
    }
}
