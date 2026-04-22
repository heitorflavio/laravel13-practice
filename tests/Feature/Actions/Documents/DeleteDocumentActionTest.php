<?php

use App\Actions\Documents\DeleteDocumentAction;
use App\Models\Document;
use App\Models\File;

test('deletes the document', function () {
    $document = Document::factory()->create();

    (new DeleteDocumentAction)->handle($document);

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('deletes document along with its files', function () {
    $document = Document::factory()->create();
    $file = File::factory()->create(['document_id' => $document->id]);

    (new DeleteDocumentAction)->handle($document);

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    $this->assertDatabaseMissing('files', ['id' => $file->id]);
});
