<?php

namespace App\Jobs;

use App\Ai\Agents\DocumentSummarizer;
use App\Models\Document;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SummarizeDocument implements ShouldQueue
{
    use Queueable;

    public function __construct(public Document $document) {}

    public function handle(): void
    {
        $summaries = $this->document->files()
            ->whereNotNull('ia_resume')
            ->pluck('ia_resume');

        if ($summaries->isEmpty()) {
            return;
        }

        $prompt = "Here are the summaries of each file belonging to this document:\n\n"
            . $summaries->map(fn ($s, $i) => '- File ' . ($i + 1) . ': ' . $s)->implode("\n");

        $response = (new DocumentSummarizer)->prompt($prompt);

        $this->document->update(array_filter([
            'name'        => $response['name'],
            'ia_resume'   => $response['summary'],
            'date'        => $response['date'] ?: null,
            'doctor_name' => $response['doctor_name'] ?: null,
            'specialty'   => $response['specialty'] ?: null,
            'cid10'       => $response['cid10'] ?: null,
        ]));
    }
}
