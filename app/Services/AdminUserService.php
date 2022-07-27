<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Admin\CreateAdminRequestDTO;
use App\Models\JWT_Token;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminUserService
{

	public function __construct(protected FileService $fileService)
	{
	}

	/**
	 * 
	 * @throws \Exception
	 */
	public function createAdminUser(CreateAdminRequestDTO $dto): void
	{

		if (!$this->fileService->avatarExists($dto->avatar)) {
			throw new Exception("Avatar not found", 404);
		}

		try {
			User::create([
				'uuid' => Uuid::uuid4()->toString(),
				'first_name' => $dto->first_name,
				'last_name' => $dto->last_name,
				'is_admin' => true,
				'password' => Hash::make($dto->password),
				'email' => $dto->email,
				'avatar' => $dto->avatar,
				'address' => $dto->address,
				'phone_number' => $dto->phone_number,
				"marketing" => $dto->marketing ?? false
			]);
		} catch (Exception $e) {
			Log::error("PEST-SHOP-API::error",[
				"message"=>"Failed to create admin user in the AdminUserService class",
				"dto"=>$dto,
				"exception"=>$e
			]);
			throw new Exception("Failed to create admin user", 500);
		}
	}
}
