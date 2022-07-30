<?php

use App\DTOs\User\UpdateUserRequestDTO;
use App\Models\File;
use App\Models\JWT_Token;
use App\Models\User;
use App\Services\AuthTokenService;
use App\Services\FileService;
use App\Services\NormalUserService;
use Database\Seeders\FileSeeder;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {

    $this->seed([
        FileSeeder::class
    ]);

    $authTokenService = new AuthTokenService();
    $fileService = new FileService();

    $this->normalUserService = new NormalUserService($fileService, $authTokenService);
});

it('that the service in charge of updating a normal user works properly', function () {

    $user = User::factory()->create([
        "is_admin" => false
    ]);

    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "password" => "password",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
        "marketing" => true
    ];

    $dto = new UpdateUserRequestDTO($requestData);

    expect(function () use ($dto, $user) {
        $this->normalUserService->updateUser($dto, $user->uuid);
    })->not->toThrow(Exception::class)
        ->and($user->fresh())
        ->first_name->toBe($dto->first_name)
        ->last_name->toBe($dto->last_name)
        ->email->toBe($dto->email)
        ->avatar->toBe($dto->avatar)
        ->phone_number->toBe($dto->phone_number)
        ->is_marketing->toBe(intval($dto->marketing));
});

it('that the service in charge of updating a user can\'t update an admin user', function () {

    $user = User::factory()->create([
        "is_admin" => true
    ]);

    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "password" => "password",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
        "marketing" => true
    ];

    $dto = new UpdateUserRequestDTO($requestData);

    expect(function () use ($dto, $user) {
        $this->normalUserService->updateUser($dto, $user->uuid);
    })->toThrow(Exception::class, "Admin users can't be edited")
        ->and($user->fresh())
        ->first_name->toBe($user->first_name)
        ->last_name->toBe($user->last_name)
        ->email->toBe($user->email)
        ->avatar->toBe($user->avatar)
        ->phone_number->toBe($user->phone_number)
        ->is_marketing->toBe(intval($user->is_marketing));
});

it('that the normal user service can delete a user with their relationships', function () {

    $user = User::factory()->create([
        "is_admin" => false
    ]);

    expect(function () use ($user) {
        $this->normalUserService->deleteUser($user->uuid);
    })->not->toThrow(Exception::class)
        ->and($user->fresh())->toBeNull();

    assertDatabaseMissing((new JWT_Token())->getTable(), [
        "user_id" => $user->id
    ]);

    assertDatabaseMissing((new User())->getTable(), [
        "id" => $user->id
    ]);
});

it('that the normal user service can\'t delete an admin user', function () {

    $user = User::factory()->create([
        "is_admin" => true
    ]);

    expect(function () use ($user) {
        $this->normalUserService->deleteUser($user->uuid);
    })->toThrow(Exception::class, "Admin users can't be deleted")
        ->and($user->fresh())->not->toBeNull();

    assertDatabaseHas((new User())->getTable(), [
        "id" => $user->id
    ]);
});
