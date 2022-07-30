<?php

use App\Models\File;
use App\Models\User;
use App\Models\JWT_Token;
use Database\Seeders\FileSeeder;
use App\Services\AuthTokenService;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;
use function Pest\Laravel\postJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->authTokenService = new AuthTokenService();
    $this->seed([
        FileSeeder::class,
    ]);
});

it('can create a new admin user', function (): void {
    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => "admin3@gmail.com",
        "password" => "password",
        "password_confirmation" => "password",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];
    $response = $this->post('/api/v1/admin/create', $requestData);

    $response->assertStatus(200);

    expect($response->json())
        ->toHaveKey('message')
        ->message->toBe('Admin user created successfully');

    unset($requestData["password"]);
    unset($requestData["password_confirmation"]);

    assertDatabaseHas((new User())->getTable(), $requestData);
});

it('can\'t create a new admin user with same email as an existing user', function (): void {
    $email = "admin3@gmail.com";
    User::factory()->create([
        "email" => $email,
    ]);
    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => $email,
        "password" => "password",
        "password_confirmation" => "password",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];
    $response = $this->post('/api/v1/admin/create', $requestData);

    $response->assertStatus(422);

    unset($requestData["password"]);
    unset($requestData["password_confirmation"]);

    assertDatabaseMissing((new User())->getTable(), $requestData);
});

it('can login as admin user', function (): void {
    $email = "admin3@gmail.com";
    User::factory()->create([
        "email" => $email,
        "password" => Hash::make("password"),
        "is_admin" => true,
    ]);

    $requestData = [
        "email" => $email,
        "password" => "password",
    ];

    $response = postJson('/api/v1/admin/login', $requestData);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "message",
        "token",
    ]);

    expect($response->json()["message"])->toBe("Login successful");

    // test token with protected endpoint
    $response2 = get('/api/v1/admin/user-listing', ["Authorization" => "Bearer " . $response->json()["token"]]);

    $response2->assertStatus(200);
});

it('can\'t login as admin user with wrong credentials', function (): void {
    $email = "admin3@gmail.com";
    User::factory()->create([
        "email" => $email,
        "password" => Hash::make("password"),
        "is_admin" => true,
    ]);

    $requestData = [
        "email" => $email,
        "password" => "123456",
    ];

    $response = postJson('/api/v1/admin/login', $requestData);

    $response->assertStatus(405);

    $response->assertJsonStructure([
        "message",
    ]);

    expect($response->json()["message"])->toBe("Wrong password");
});

it('can\'t access protected endpoints with wrong token', function (): void {

    // test token with protected endpoint
    $response2 = get('/api/v1/admin/user-listing', ["Authorization" => "Bearer xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"]);

    $response2->assertStatus(401);
});

it('can logout', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    // test token with protected endpoint
    $response2 = get('/api/v1/admin/logout', ["Authorization" => "Bearer {$token}"]);

    $response2->assertStatus(200);

    assertDatabaseMissing((new JWT_Token())->getTable(), [
        "user_id" => $user->id,
    ]);

    // test token with protected endpoint
    $response2 = get('/api/v1/admin/user-listing', ["Authorization" => "Bearer {$token}"]);

    $response2->assertStatus(401);
});

it('can list normal users with pagination', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    User::factory(20)->create([
        "is_admin" => false,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    // test token with protected endpoint
    $response = get('/api/v1/admin/user-listing', ["Authorization" => "Bearer {$token}"]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "current_page",
        "data" => [
            [

                "uuid",
                "first_name",
                "last_name",
                "email",
                "avatar",
                "address",
                "phone_number",
                "created_at",
                "last_login_at",

            ],
        ],
        "first_page_url",
        "from",
        "last_page",
        "last_page_url",
        "links" => [
            [
                "url",
                "label",
                "active",
            ],
        ],
        "next_page_url",
        "path",
        "per_page",
        "prev_page_url",
        "to",
        "total",
    ]);

    expect($response->json())
        ->current_page->toBeNumeric()
        ->data->toBeArray()
        ->from->toBeNumeric()
        ->last_page->toBeNumeric()
        ->per_page->toBeNumeric()
        ->prev_page_url->toBeNull()
        ->to->toBeNumeric()
        ->total->toBeNumeric();
});

it('can update a normal user', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    $email = "admin3@gmail.com";
    $userToUpdate = User::factory()->create([
        "is_admin" => false,
    ]);

    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => $email,
        "password" => "123456",
        "password_confirmation" => "123456",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];
    $response = put("/api/v1/admin/user-edit/{$userToUpdate->uuid}", $requestData, ["Authorization" => "Bearer {$token}"]);

    $response->assertStatus(200);

    expect($response->json())
        ->toHaveKey('message')
        ->message->toBe('Normal user updated successfully');

    unset($requestData["password"]);
    unset($requestData["password_confirmation"]);

    assertDatabaseHas((new User())->getTable(), $requestData);
});

it('can\'t update an admin user', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    $email = "admin3@gmail.com";
    $userToUpdate = User::factory()->create([
        "is_admin" => true,
    ]);

    $requestData = [
        "first_name" => "aaaa",
        "last_name" => "aaaa",
        "email" => $email,
        "password" => "123456",
        "password_confirmation" => "123456",
        "avatar" => File::inRandomOrder()->first()->uuid,
        "address" => "rerreer",
        "phone_number" => "rreere",
    ];
    $response = put("/api/v1/admin/user-edit/{$userToUpdate->uuid}", $requestData, ["Authorization" => "Bearer {$token}"]);

    $response->assertStatus(403);

    expect($response->json())
        ->toHaveKey('message')
        ->message->toBe('Admin users can\'t be edited');

    unset($requestData["password"]);
    unset($requestData["password_confirmation"]);

    assertDatabaseMissing((new User())->getTable(), $requestData);
});

it('can delete a normal user', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    $userToDelete = User::factory()->create([
        "is_admin" => false,
    ]);

    $response = delete("/api/v1/admin/user-delete/{$userToDelete->uuid}", [], ["Authorization" => "Bearer {$token}"]);

    $response->assertStatus(200);

    expect($response->json())
        ->toHaveKey('message')
        ->message->toBe('Normal user deleted successfully');

    assertDatabaseMissing((new User())->getTable(), [
        "id" => $userToDelete->id,
    ]);
});

it('can\'t delete an admin user', function (): void {
    $user = User::factory()->create([
        "is_admin" => true,
    ]);

    $token = $this->authTokenService->createUserToken($user);

    $userToDelete = User::factory()->create([
        "is_admin" => true,
    ]);

    $response = delete("/api/v1/admin/user-delete/{$userToDelete->uuid}", [], ["Authorization" => "Bearer {$token}"]);

    $response->assertStatus(403);

    expect($response->json())
        ->toHaveKey('message')
        ->message->toBe('Admin users can\'t be deleted');

    assertDatabaseHas((new User())->getTable(), [
        "id" => $userToDelete->id,
    ]);
});
