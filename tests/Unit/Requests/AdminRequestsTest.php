<?php

declare(strict_types=1);

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\ListUsersRequest;
use App\Http\Requests\Admin\LoginAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use function Pest\Faker\faker;

it('that the request to create an admin user is validated properly', function (string $field, ?string $value, array $dependingFields, bool $shouldFail) {
    $rules = (new CreateAdminRequest())->rules();

    $validator = Validator::make(
        [$field => $value, ...$dependingFields],
        [$field => $rules[$field]]
    );

    expect($validator->fails())->toBe($shouldFail);
})->with([

    [
        "first_name",
        function () {
            return faker()->firstName();
        },
        [],
        false,
    ],
    [
        "first_name",
        null,
        [],
        true,
    ],
    [
        "last_name",
        function () {
            return faker()->lastName();
        },
        [],
        false,
    ],
    [
        "last_name",
        null,
        [],
        true,
    ],
    [
        "email",
        function () {
            return faker()->userName() . "@gmail.com";
        },
        [],
        false,
    ],
    [
        "email",
        null,
        [],
        true,
    ],
    [
        "email",
        "username@nonexistingdomainname.com",
        [],
        true,
    ],
    [
        "email",
        function () {
            $email = faker()->userName() . "@gmail.com";
            User::factory()->create([
                "email" => $email
            ]);
            return $email;
        },
        [],
        true,
    ],
    [
        "password",
        "password",
        ["password_confirmation" => "password"],
        false,

    ],
    [
        "password",
        "123",
        [],
        true,

    ],
    [
        "password",
        "password",
        [],
        true,

    ],
    [
        "avatar",
        function () {
            return faker()->uuid();
        },
        [],
        false,
    ],
    [
        "avatar",
        null,
        [],
        true,

    ],
    [
        "address",
        function () {
            return faker()->address();
        },
        [],
        false,
    ],
    [
        "address",
        null,
        [],
        true,

    ],
    [
        "phone_number",
        function () {
            return faker()->phoneNumber();
        },
        [],
        false,
    ],
    [
        "phone_number",
        null,
        [],
        true,

    ],
    [
        "marketing",
        function () {
            return faker()->boolean();
        },
        [],
        false,
    ],
    [
        "marketing",
        null,
        [],
        false,
    ],
    [
        "marketing",
        "text",
        [],
        true,
    ],
]);

it('that the request to list normal users is validated properly', function (string $field, ?string $value, array $dependingFields, bool $shouldFail) {
    $rules = (new ListUsersRequest())->rules();

    $validator = Validator::make(
        [$field => $value, ...$dependingFields],
        [$field => $rules[$field]]
    );

    expect($validator->fails())->toBe($shouldFail);
})->with([
    [
        "page",
        function () {
            return faker()->randomNumber();
        },
        [],
        false,
    ],
    [
        "page",
        null,
        [],
        false,
    ],
    [
        "page",
        "text",
        [],
        true,
    ],

    [
        "limit",
        function () {
            return faker()->randomNumber();
        },
        [],
        false,
    ],
    [
        "limit",
        null,
        [],
        false,
    ],
    [
        "limit",
        "text",
        [],
        true,
    ],

    [
        "sortBy",
        function () {
            return faker()->word();
        },
        [],
        false,
    ],
    [
        "sortBy",
        null,
        [],
        false,
    ],

    [
        "desc",
        function () {
            return faker()->randomElement([true, false]);
        },
        [],
        false,
    ],
    [
        "desc",
        null,
        [],
        false,
    ],
    [
        "desc",
        "text",
        [],
        true,
    ],

    [
        "first_name",
        function () {
            return faker()->firstName();
        },
        [],
        false,
    ],
    [
        "first_name",
        null,
        [],
        false,
    ],

    [
        "email",
        function () {
            return faker()->email();
        },
        [],
        false,
    ],
    [
        "email",
        null,
        [],
        false,
    ],

    [
        "phone",
        function () {
            return faker()->phoneNumber();
        },
        [],
        false,
    ],
    [
        "phone",
        null,
        [],
        false,
    ],

    [
        "address",
        function () {
            return faker()->address();
        },
        [],
        false,
    ],
    [
        "address",
        null,
        [],
        false,
    ],

    [
        "created_at",
        function () {
            return faker()->date();
        },
        [],
        false,
    ],
    [
        "created_at",
        null,
        [],
        false,
    ],
    [
        "created_at",
        "text",
        [],
        true,
    ],

    [
        "marketing",
        function () {
            return faker()->randomElement([0, 1]);
        },
        [],
        false,
    ],
    [
        "marketing",
        null,
        [],
        false,
    ],
    [
        "marketing",
        4,
        [],
        true,
    ],
]);

it('that the request to login admin users is validated properly', function (string $field, ?string $value, array $dependingFields, bool $shouldFail) {
    $rules = (new LoginAdminRequest())->rules();

    $validator = Validator::make(
        [$field => $value, ...$dependingFields],
        [$field => $rules[$field]]
    );

    expect($validator->fails())->toBe($shouldFail);
})->with([
    [
        "email",
        function () {
            return faker()->userName() . "@gmail.com";
        },
        [],
        false,
    ],
    [
        "password",
        function () {
            return faker()->password();
        },
        [],
        false,
    ],
    [
        "email",
        function () {
            return faker()->userName() . "@nonexistingrandomdomainname.com";
        },
        [],
        true,
    ],
    [
        "email",
        null,
        [],
        true,
    ],
    [
        "password",
        function () {
            return faker()->password(1,3);
        },
        [],
        true,
    ],
    [
        "password",
        null,
        [],
        true,
    ],
]);
