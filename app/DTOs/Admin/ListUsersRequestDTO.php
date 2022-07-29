<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use App\DTOs\BaseDTO;

class ListUsersRequestDTO extends BaseDTO
{

    public int $page = 1;
    public int $limit = 10;
	public string $sortBy = 'id';
	public bool $desc = false;
	public ?string $first_name = null;
	public ?string $email = null;
	public ?string $phone = null;
	public ?string $address = null;
	public ?string $created_at = null;
	public bool $marketing = false;

	public function __construct(array $requestData)
	{
		parent::__construct($requestData);
	}
}
