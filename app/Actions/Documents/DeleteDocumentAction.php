<?php

namespace App\Actions\Documents;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DeleteDocumentAction
{
    public function handle(Document $document): void
    {
        DB::transaction(fn () => $document->delete());
    }
}
