<?php

use App\Actions\Documents\DeleteFileAction;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

test('removes file from storage and database', function () {
    Storage::fake('public');

    $path = 'documents/test.pdf';
    Storage::disk('public')->put($path, 'content');

    $file = File::factory()->create(['file_path' => $path]);

    (new DeleteFileAction())->handle($file);

    Storage::disk('public')->assertMissing($path);
    $this->assertDatabaseMissing('files', ['id' => $file->id]);
});

test('deletes database record even when file does not exist in storage', function () {
    Storage::fake('public');

    $file = File::factory()->create(['file_path' => 'documents/missing.pdf']);

    (new DeleteFileAction())->handle($file);

    $this->assertDatabaseMissing('files', ['id' => $file->id]);
});
