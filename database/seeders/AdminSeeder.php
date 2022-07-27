<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'is_admin' => true,
            'avatar' => File::inRandomOrder()->first() ?? File::factory()->create(),
        ]);
    }
}
