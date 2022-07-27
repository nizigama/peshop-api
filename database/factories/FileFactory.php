<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $uuid = $this->faker->unique()->uuid;
        $extension = $this->faker->randomElement(["png","jpg","jpeg"]);
        return [
            "uuid" => $uuid, 
            "name" => $this->faker->name, 
            "path" => storage_path("app/public/$uuid.$extension"),
            "size" => $this->faker->numberBetween(10,100) . " KB",
            "type" => "$extension"
        ];
    }
}
