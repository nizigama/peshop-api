<?php

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\User\UpdateUserRequest;
use function Pest\Faker\faker;

it('that the request to update a normal user is validated properly', function (string $field, ?string $value, array $dependingFields, bool $shouldFail): void {
    $rules = (new UpdateUserRequest())->rules();

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
