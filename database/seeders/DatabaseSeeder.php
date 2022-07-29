<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\JWT_Token;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(50)->create([
            'is_admin' => false
        ]);
        
        $this->call([
            FileSeeder::class,
            AdminSeeder::class
        ]);
    }
}
