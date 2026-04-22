<?php

namespace App\Actions\Documents;

use App\DTOs\Documents\CreateDocumentData;
use App\Models\Document;

class CreateDocumentAction
{
    public function handle(CreateDocumentData $data): Document
    {
        $count = Document::where('user_id', $data->userId)->count();

        return Document::create([
            'user_id' => $data->userId,
            'name'    => 'New Document ' . ($count + 1),
        ]);
    }
}
