<?php

namespace App\Jobs;

use App\Ai\Agents\FileSummarizer;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Files\Document;

class SummarizeFile implements ShouldQueue
{
    use Queueable;

    public function __construct(public File $file) {}

    public function handle(): void
    {
        $this->file->update(['status' => 'processing']);

        $fullPath = Storage::disk('public')->path($this->file->file_path);

        $response = (new FileSummarizer)->prompt(
            'Summarize the content of this file.',
            attachments: [
                Document::fromPath($fullPath),
            ]
        );

        $this->file->update([
            'ia_resume' => $response['summary'],
            'status' => 'done',
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->file->update(['status' => 'error']);
    }
}
