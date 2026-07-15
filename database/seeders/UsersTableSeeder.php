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
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@salonjc.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_verified' => true,
            ]
        );

        // Create regular user if not exists
        User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'is_verified' => true,
            ]
        );

        // Seed salons across popular cities
        $salons = [
            [
                'name' => 'Mumbai Glow Salon Owner',
                'email' => 'mumbai@salonjc.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'salon_name' => 'Mumbai Glow Salon',
                'salon_type' => 'Unisex Salon',
                'salon_model' => 'Self Owned',
                'location' => 'Mumbai',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'is_verified' => true,
            ],
            [
                'name' => 'Delhi Royal Spa Owner',
                'email' => 'delhi@salonjc.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'salon_name' => 'Delhi Royal Spa',
                'salon_type' => 'Makeup Studio',
                'salon_model' => 'Franchisee',
                'location' => 'Delhi-NCR',
                'latitude' => 28.6139,
                'longitude' => 77.2090,
                'is_verified' => true,
            ],
            [
                'name' => 'Bengaluru Hair Studio Owner',
                'email' => 'bengaluru@salonjc.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'salon_name' => 'Bengaluru Hair Studio',
                'salon_type' => 'Mens Salon',
                'salon_model' => 'Self Owned',
                'location' => 'Bengaluru',
                'latitude' => 12.9716,
                'longitude' => 77.5946,
                'is_verified' => true,
            ],
            [
                'name' => 'Hyderabad Wellness Owner',
                'email' => 'hyderabad@salonjc.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'salon_name' => 'Hyderabad Wellness Center',
                'salon_type' => 'Unisex Salon',
                'salon_model' => 'Self Owned',
                'location' => 'Hyderabad',
                'latitude' => 17.3850,
                'longitude' => 78.4867,
                'is_verified' => true,
            ],
            [
                'name' => 'Kochi Elite Salon Owner',
                'email' => 'kochi@salonjc.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'salon_name' => 'Kochi Elite Salon',
                'salon_type' => 'Female Salon',
                'salon_model' => 'Franchisee',
                'location' => 'Kochi',
                'latitude' => 9.9312,
                'longitude' => 76.2673,
                'is_verified' => true,
            ],
        ];

        foreach ($salons as $salon) {
            User::firstOrCreate(
                ['email' => $salon['email']],
                array_merge($salon, ['email_verified_at' => now()])
            );
        }

        // Create more sample users
        User::factory(8)->create();
    }
}
