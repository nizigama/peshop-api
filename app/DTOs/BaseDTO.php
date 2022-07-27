<?php

declare(strict_types=1);

namespace App\DTOs;

use ReflectionClass;
use ReflectionProperty;

abstract class BaseDTO
{

	public function __construct(array $requestData)
	{
		foreach (array_keys($requestData) as $key) {
			$this->$key = $requestData[$key];
		}

		$obj = new ReflectionClass($this);

		$properties = $obj->getProperties(ReflectionProperty::IS_PUBLIC);


		foreach ($properties as $property) {

			if (!$property->isInitialized($this)) {
				$name = $property->getName();
				throw new \Exception("$name was not initialized");
			}
		}

	}
}
