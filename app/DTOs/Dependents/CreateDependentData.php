<?php

namespace App\DTOs\Dependents;

readonly class CreateDependentData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $parentId,
    ) {}
}
