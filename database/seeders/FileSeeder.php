<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availableFiles = [
            [
                "uuid" => "128934-438934-4389834-348934",
                "name" => "first avatar",
                "path" => "app/public/128934-438934-4389834-348934.jpg",
                "size" => "7.3 KB",
                "type" => "jpg"
            ],[
                "uuid" => "594054-54904-543672-5862",
                "name" => "second avatar",
                "path" => "app/public/594054-54904-543672-5862.png",
                "size" => "29.8 KB",
                "type" => "png"
            ],[
                "uuid" => "53a0026f-c1f5-4b9a-a0cb-c63d9d027ee3",
                "name" => "radar",
                "path" => "app/public/53a0026f-c1f5-4b9a-a0cb-c63d9d027ee3.jpg",
                "size" => "473.5 KB",
                "type" => "jpg"
            ],
        ];

        foreach ($availableFiles as $value) {
            File::factory()->create($value);
        }
    }
}
