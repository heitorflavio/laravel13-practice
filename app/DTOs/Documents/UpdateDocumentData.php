<?php

namespace App\DTOs\Documents;

readonly class UpdateDocumentData
{
    public function __construct(
        public string $name,
        public string $content,
    ) {}
}
