<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use App\DTOs\BaseDTO;

class LoginAdminRequestDTO extends BaseDTO
{
    public string $email;
    public string $password;
}
