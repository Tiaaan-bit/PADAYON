<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Admin1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::updateOrCreate(
            ['email' => 'admin1@admin1.com'],
            [
                'name'              => 'Admin',
                'phone'             => '09000000000',
                'password'          => Hash::make('password1'),
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
 
        $this->command->info('Admin created: admin1@admin1.com / password1');
    }
}
