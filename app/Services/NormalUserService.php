<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\JWT_Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\DTOs\User\UpdateUserRequestDTO;

class NormalUserService
{
    public function __construct(protected FileService $fileService, protected AuthTokenService $authTokenService)
    {
    }

    /**
     * @throws Exception
     */
    public function updateUser(UpdateUserRequestDTO $dto, string $userUuid): bool
    {
        $user = User::where("uuid", $userUuid)->first();

        if (is_null($user)) {
            throw new Exception("User not found", 404);
        }

        if ($user->is_admin) {
            throw new Exception("Admin users can't be edited", 403);
        }

        if (!$this->fileService->avatarExists($dto->avatar)) {
            throw new Exception("Avatar not found", 404);
        }

        $userWithGivenEmail = User::where([["email", $dto->email], ["uuid", "!=", $userUuid]])->exists();

        if ($userWithGivenEmail) {
            throw new Exception("Email is already taken", 403);
        }

        try {
            $user->first_name = $dto->first_name;
            $user->last_name = $dto->last_name;
            $user->email = $dto->email;
            $user->password = Hash::make($dto->password);
            $user->avatar = $dto->avatar;
            $user->address = $dto->address;
            $user->phone_number = $dto->phone_number;
            $user->is_marketing = $dto->marketing ?? false;
            return $user->save();
        } catch (\Exception $e) {
            Log::error("PEST-SHOP-API::error", [
                "message" => "Failed to update normal user in the NormalUserService",
                "dto" => $dto,
                "exception" => $e,
            ]);
            throw new Exception("Failed to update the user", 500);
        }
    }

    /**
     * @throws Exception
     */
    public function deleteUser(string $uuid): void
    {
        $user = User::where("uuid", $uuid)->first();

        if (is_null($user)) {
            throw new Exception("User not found", 404);
        }

        if ($user->is_admin) {
            throw new Exception("Admin users can't be deleted", 403);
        }

        try {
            DB::transaction(function () use ($user): void {
                JWT_Token::where("user_id", $user->id)->delete();
                $user->delete();
            });
        } catch (\Exception $e) {
            Log::error("PEST-SHOP-API::error", [
                "message" => "Failed to delete normal user in the NormalUserService class",
                "userUuid" => $uuid,
                "exception" => $e,
            ]);
            throw new Exception("Failed to delete the user", 500);
        }
    }
}
