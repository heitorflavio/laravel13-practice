<?php

use App\Jobs\SummarizeDocument;
use App\Jobs\SummarizeFile;
use App\Livewire\Documents\EditDocuments;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('mounts with document data', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create([
        'user_id' => $user->id,
        'name' => 'My Exam',
    ]);

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->assertSet('title', 'My Exam')
        ->assertSet('id', $document->id);
});

test('cannot mount with another user document', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id]);
})->throws(ModelNotFoundException::class);

test('saves document with updated name', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->set('title', 'Updated Title')
        ->call('save');

    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'name' => 'Updated Title',
    ]);
});

test('uploads files and dispatches summarize jobs', function () {
    Storage::fake('public');
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('exam.pdf', 100, 'application/pdf');

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->set('files', [$file])
        ->call('saveFiles');

    $this->assertDatabaseHas('files', ['document_id' => $document->id]);
    Queue::assertPushed(SummarizeFile::class);
});

test('deletes a file', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);
    $file = File::factory()->create([
        'document_id' => $document->id,
        'file_path' => 'documents/test.pdf',
    ]);
    Storage::disk('public')->put('documents/test.pdf', 'content');

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->call('deleteFile', $file->id);

    $this->assertDatabaseMissing('files', ['id' => $file->id]);
    Storage::disk('public')->assertMissing('documents/test.pdf');
});

test('dispatches summarize document job when files have resumes', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);
    File::factory()->done()->create(['document_id' => $document->id]);

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->call('summarizeDocument');

    Queue::assertPushed(SummarizeDocument::class);
});

test('does not dispatch summarize job when no file has a resume', function () {
    Queue::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);
    File::factory()->create(['document_id' => $document->id]);

    Livewire::actingAs($user)
        ->test(EditDocuments::class, ['id' => $document->id])
        ->call('summarizeDocument');

    Queue::assertNotPushed(SummarizeDocument::class);
});
