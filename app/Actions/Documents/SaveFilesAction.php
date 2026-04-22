<?php

namespace App\Actions\Documents;

use App\DTOs\Documents\SaveFilesData;
use App\Jobs\SummarizeFile;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaveFilesAction
{
    public function handle(SaveFilesData $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data->files as $file) {
                $path = $file->store('documents', 'public');

                $fileRecord = File::create([
                    'document_id' => $data->documentId,
                    'file_path' => $path,
                    'file_url' => Storage::disk('public')->url($path),
                    'mime_type' => $file->getClientMimeType(),
                ]);

                SummarizeFile::dispatch($fileRecord);
            }
        });
    }
}
