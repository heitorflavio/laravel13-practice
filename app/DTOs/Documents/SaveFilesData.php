<?php

namespace App\DTOs\Documents;

readonly class SaveFilesData
{
    public function __construct(
        public int $documentId,
        public array $files,
    ) {}
}
