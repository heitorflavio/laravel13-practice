<?php

namespace App\Actions\Documents;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class DeleteFileAction
{
    public function handle(File $file): void
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
    }
}
