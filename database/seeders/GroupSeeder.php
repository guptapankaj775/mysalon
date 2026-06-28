<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Group::firstOrCreate(['name' => 'Creditor']);
        \App\Models\Group::firstOrCreate(['name' => 'Debtor']);
    }
}
