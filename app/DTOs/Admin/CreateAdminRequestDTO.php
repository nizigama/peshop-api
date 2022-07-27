<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use App\DTOs\BaseDTO;

class CreateAdminRequestDTO extends BaseDTO
{

	public string $first_name;
	public string $last_name;
	public string $email;
	public string $password;
	public string $avatar;
	public string $address;
	public string $phone_number;
	public bool $marketing;

	public function __construct(array $requestData)
	{
		parent::__construct($requestData);
	}
}
