<?php

namespace App\DTOs\Documents;

readonly class SaveFilesData
{
    public function __construct(
        public int $documentId,
        /** @var array<int, mixed> */
        public array $files,
    ) {}
}
