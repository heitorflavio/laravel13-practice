<?php

use App\Actions\Documents\UpdateDocumentAction;
use App\DTOs\Documents\UpdateDocumentData;
use App\Models\Document;
use App\Models\User;

test('updates document name', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);

    (new UpdateDocumentAction)->handle($document, new UpdateDocumentData(
        name: 'New Name',
        content: '',
    ));

    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'name' => 'New Name',
    ]);
});

test('returns the updated document', function () {
    $document = Document::factory()->create();

    $updated = (new UpdateDocumentAction)->handle($document, new UpdateDocumentData(
        name: 'Updated',
        content: '',
    ));

    expect($updated)->toBeInstanceOf(Document::class)
        ->and($updated->name)->toBe('Updated');
});
