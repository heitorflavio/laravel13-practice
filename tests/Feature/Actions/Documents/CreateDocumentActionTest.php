<?php

use App\Actions\Documents\CreateDocumentAction;
use App\DTOs\Documents\CreateDocumentData;
use App\Models\Document;
use App\Models\User;

test('creates a document for the given user', function () {
    $user = User::factory()->create();

    $document = (new CreateDocumentAction())->handle(
        new CreateDocumentData(userId: $user->id)
    );

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->user_id)->toBe($user->id)
        ->and($document->name)->toBe('New Document 1');

    $this->assertDatabaseHas('documents', [
        'user_id' => $user->id,
        'name'    => 'New Document 1',
    ]);
});

test('increments document name based on existing count', function () {
    $user = User::factory()->create();
    Document::factory()->count(3)->create(['user_id' => $user->id]);

    $document = (new CreateDocumentAction())->handle(
        new CreateDocumentData(userId: $user->id)
    );

    expect($document->name)->toBe('New Document 4');
});

test('does not count documents from other users', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();
    Document::factory()->count(5)->create(['user_id' => $other->id]);

    $document = (new CreateDocumentAction())->handle(
        new CreateDocumentData(userId: $user->id)
    );

    expect($document->name)->toBe('New Document 1');
});
