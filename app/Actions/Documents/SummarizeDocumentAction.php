<?php

namespace App\Actions\Documents;

use App\Jobs\SummarizeDocument;
use App\Models\Document;

class SummarizeDocumentAction
{
    public function handle(Document $document): void
    {
        SummarizeDocument::dispatch($document);
    }
}
