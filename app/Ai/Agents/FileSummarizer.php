<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class FileSummarizer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'You are a document summarizer. When given a file, extract a concise summary of its content. Respond in the same language as the document.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'summary' => $schema->string()->required(),
        ];
    }
}
