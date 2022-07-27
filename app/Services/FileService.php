<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\File;

class FileService
{

	public function avatarExists(string $uuid): bool
	{
		return File::where("uuid", $uuid)->exists();
	}
}
