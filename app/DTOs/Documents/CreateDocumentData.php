<?php

namespace App\DTOs\Documents;

readonly class CreateDocumentData
{
    public function __construct(
        public int $userId,
    ) {}
}
