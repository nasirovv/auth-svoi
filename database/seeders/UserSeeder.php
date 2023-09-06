<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create(
            [
                'login'            => 'admin123',
                'password'         => bcrypt('admin123'),
                'telephone_number' => '998936820017',
                'status'           => 'active',
                'role_id'          => 1,
                'auth_step'        => 'login'
            ]
        );
    }
}
