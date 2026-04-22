<?php

namespace App\Actions\Documents;

use App\DTOs\Documents\UpdateDocumentData;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class UpdateDocumentAction
{
    public function handle(Document $document, UpdateDocumentData $data): Document
    {
        return DB::transaction(function () use ($document, $data) {
            $document->update([
                'name'    => $data->name,
                'content' => $data->content,
            ]);

            return $document->fresh();
        });
    }
}
