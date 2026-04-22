<?php

use App\Actions\Documents\SummarizeDocumentAction;
use App\Jobs\SummarizeDocument;
use App\Models\Document;
use Illuminate\Support\Facades\Queue;

test('dispatches SummarizeDocument job', function () {
    Queue::fake();

    $document = Document::factory()->create();

    (new SummarizeDocumentAction)->handle($document);

    Queue::assertPushed(SummarizeDocument::class);
});
