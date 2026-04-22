<?php

use App\Actions\Documents\SaveFilesAction;
use App\DTOs\Documents\SaveFilesData;
use App\Jobs\SummarizeFile;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('stores files and persists records to database', function () {
    Storage::fake('public');
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('exam.pdf', 100, 'application/pdf');

    (new SaveFilesAction)->handle(new SaveFilesData(
        documentId: $document->id,
        files: [$file],
    ));

    $this->assertDatabaseHas('files', [
        'document_id' => $document->id,
        'mime_type' => 'application/pdf',
        'status' => 'pending',
    ]);
});

test('dispatches SummarizeFile job for each uploaded file', function () {
    Storage::fake('public');
    Queue::fake();

    $document = Document::factory()->create();
    $files = [
        UploadedFile::fake()->create('a.pdf', 50, 'application/pdf'),
        UploadedFile::fake()->create('b.pdf', 50, 'application/pdf'),
    ];

    (new SaveFilesAction)->handle(new SaveFilesData(
        documentId: $document->id,
        files: $files,
    ));

    Queue::assertPushed(SummarizeFile::class, 2);
});
