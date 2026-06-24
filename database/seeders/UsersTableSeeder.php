<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user (or update if it already exists)
        User::updateOrCreate(
            ['email' => 'admin@salonjc.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'), // change as needed
                'role' => 'superadmin', // grant all permissions – adjust if your app uses a different role name
                'email_verified_at' => now(),
            ]
        );

        // Create regular user
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create more sample users
        User::factory(8)->create();
    }
}
