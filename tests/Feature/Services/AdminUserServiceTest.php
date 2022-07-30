<?php

use App\Models\File;
use App\Models\User;
use App\Models\JWT_Token;
use App\Services\FileService;
use Database\Seeders\FileSeeder;
use App\Services\AdminUserService;
use App\Services\AuthTokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Query\Builder;
use App\DTOs\Admin\ListUsersRequestDTO;
use App\DTOs\Admin\LoginAdminRequestDTO;
use App\DTOs\Admin\CreateAdminRequestDTO;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->authTokenService = new AuthTokenService();
    $fileService = new FileService();

    $this->seed([
        FileSeeder::class,
    ]);

    $this->adminUserService = new AdminUserService($fileService, $this->authTokenService);
});

it('that the admin user service can create an admin user', function (): void {
    $avatar = File::InRandomOrder()->first()->uuid;
    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "password" => "password",
        "password_confirmation" => "password",
        "avatar" => $avatar,
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];

    $dto = new CreateAdminRequestDTO($requestData);

    expect(function () use ($dto): void {
        $this->adminUserService->createAdminUser($dto);
    })->not->toThrow(Exception::class);

    $userData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "avatar" => $avatar,
        "address" => "rerreer",
        "phone_number" => "rreere",
        "is_admin" => true,
    ];

    assertDatabaseHas((new User())->getTable(), $userData);
});

it('that the admin user service throws an exception when an invalid avatar uuid is passed', function (): void {
    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "password" => "password",
        "password_confirmation" => "password",
        "avatar" => 'wqo-4390-reoi-4390',
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];

    $dto = new CreateAdminRequestDTO($requestData);

    expect(function () use ($dto): void {
        $this->adminUserService->createAdminUser($dto);
    })->toThrow(Exception::class, "Avatar not found");

    $userData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "avatar" => 'wqo-4390-reoi-4390',
        "address" => "rerreer",
        "phone_number" => "rreere",
        "is_admin" => true,
    ];

    assertDatabaseMissing((new User())->getTable(), $userData);
});

it('that the admin user service can create a valid token for admin user', function (): void {
    $user = User::factory()->create([
        "email" => "admin3@gmail.com",
        "password" => Hash::make("password"),
        "is_admin" => true,
    ]);

    $requestData = [
        "email" => "admin3@gmail.com",
        "password" => "password",
    ];

    $dto = new LoginAdminRequestDTO($requestData);

    expect(function () use ($dto): void {
        $this->adminUserService->loginAdminUser($dto);
    })->not->toThrow(Exception::class);

    $token = $this->adminUserService->loginAdminUser($dto);

    $tokenValid = $this->authTokenService->isTokenValid($token);

    expect($token)->not->toBeNull()
        ->and($tokenValid)->not->toBeFalse();

    assertDatabaseHas((new JWT_Token())->getTable(), [
        "user_id" => $user->id,
        "token_title" => "ADMIN LOGIN TOKEN",
    ]);

    assertDatabaseMissing((new User())->getTable(), [
        'uuid' => $user->uuid,
        'last_login_at' => null,
    ]);
});

it('that the admin user service can delete an admin user token', function (): void {
    $user = User::factory()->create([
        "email" => "admin3@gmail.com",
        "password" => Hash::make("password"),
        "is_admin" => true,
    ]);

    $requestData = [
        "email" => "admin3@gmail.com",
        "password" => "password",
    ];

    $dto = new LoginAdminRequestDTO($requestData);

    $this->adminUserService->loginAdminUser($dto);

    expect($this->adminUserService->logoutUser($user))->toBeTrue();

    assertDatabaseMissing((new JWT_Token())->getTable(), [
        "user_id" => $user->id,
        "token_title" => "ADMIN LOGIN TOKEN",
    ]);
});

it('that the admin user service can list normal users with matching passed query', function (ListUsersRequestDTO $dto): void {
    expect(function () use ($dto): void {
        $this->adminUserService->listNormalUsers($dto);
    })->not->toThrow(Exception::class, "You are not allowed to sort by that value")
        ->and($this->adminUserService->listNormalUsers($dto))
        ->toBeInstanceOf(Builder::class)
        ->first()->email->toBe($dto->email);
})->with([
    function () {
        User::factory(30)->create([
            "is_admin" => false,
        ]);

        $randomUser = User::inRandomOrder()->first();

        return new ListUsersRequestDTO([
            'first_name' => $randomUser->first_name,
            'last_name' => $randomUser->last_name,
            'email' => $randomUser->email,
            'avatar' => $randomUser->avatar,
            'address' => $randomUser->address,
            'phone_number' => $randomUser->phone_number,
        ]);
    },
]);

it('that the admin user service throws exception when invalid value is passed as sort by query parameter', function (): void {
    $dto = new ListUsersRequestDTO([
        'sortBy' => 'random',
    ]);

    expect(function () use ($dto): void {
        $this->adminUserService->listNormalUsers($dto);
    })->toThrow(Exception::class, "You are not allowed to sort by that value");
});
