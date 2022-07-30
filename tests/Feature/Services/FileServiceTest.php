<?php

use App\Models\File;
use App\Services\FileService;
use function Pest\Faker\faker;

it('that the file service can verify the existence of a file by its uuid properly', function (string $uuid, bool $exists): void {
    $service = new FileService();

    expect($service->avatarExists($uuid))->toBe($exists);
})->with([
    [
        function () {
            $file = File::factory()->create([
                "uuid" => "128934-438934-4389834-348934",
                "name" => "first avatar",
                "path" => "app/public/128934-438934-4389834-348934.jpg",
                "size" => "7.3 KB",
                "type" => "jpg",
            ]);
            return $file->uuid;
        },
        true,
    ],
    [
        function () {
            return faker()->uuid();
        },
        false,
    ],
]);
