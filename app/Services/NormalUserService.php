<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Admin\CreateAdminRequestDTO;
use App\DTOs\Admin\ListUsersRequestDTO;
use App\DTOs\Admin\LoginAdminRequestDTO;
use App\DTOs\User\UpdateUserRequestDTO;
use App\Models\JWT_Token;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        if ($user->is_admin === 1) {
            throw new Exception("Admin users can't be edited", 403);
        }

        if (!$this->fileService->avatarExists($dto->avatar)) {
            throw new Exception("Avatar not found", 404);
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
        } catch (\Exception $e) {
            Log::error("PEST-SHOP-API::error", [
                "message" => "Failed to update normal user in the NormalUserService",
                "dto" => $dto,
                "exception" => $e
            ]);
            throw new Exception("Failed to update the user", 500);
        }

        return $user->save();
    }

    /**
     * @throws Exception
     */
    public function deleteUser(string $uuid)
    {
        $user = User::where("uuid", $uuid)->first();

        if (is_null($user)) {
            throw new Exception("User not found", 404);
        }

        if ($user->is_admin === 1) {
            throw new Exception("Admin users can't be deleted", 403);
        }

        /**
         * @todo check if the user isn't linked to other resources
         */

        try {
            $user->delete();
        } catch (\Exception $e) {
            Log::error("PEST-SHOP-API::error", [
                "message" => "Failed to delete normal user in the NormalUserService class",
                "userUuid" => $uuid,
                "exception" => $e
            ]);
            throw new Exception("Failed to delete the user", 500);
        }
    }
}
