<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      
        Role::create(['name' => 'admin']);
Role::create(['name' => 'landlord']);
Role::create(['name' => 'user']);

User::create([
    "name" => "admin",
    "email" => "admin@gmail.com",
    "password" => Hash::make("123456"), // 🔥 Hash the password
    "role_id" => 1
]);
    }
}
