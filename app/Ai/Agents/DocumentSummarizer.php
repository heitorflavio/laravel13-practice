<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class DocumentSummarizer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You are a medical document analyst. Given a list of summaries from files belonging to the same medical document, extract and consolidate the following information:

        - name: a short descriptive title for the document (e.g. "Echocardiogram - Dr. Silva - 2026-01-15"), generated from the content
        - summary: a single cohesive paragraph summarizing the most important medical findings
        - date: the exam or consultation date found in the document (ISO 8601 format YYYY-MM-DD), or empty string if not found
        - doctor_name: the full name of the doctor/physician mentioned, or empty string if not found
        - specialty: the medical specialty (e.g. Cardiology, Orthopedics), or empty string if not found
        - cid10: the ICD-10 / CID-10 code mentioned (e.g. J45, M54.5), or empty string if not found

        Respond in the same language as the summaries. Return an empty string "" for any field you cannot determine from the content.
        PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->required(),
            'summary' => $schema->string()->required(),
            'date' => $schema->string()->required(),
            'doctor_name' => $schema->string()->required(),
            'specialty' => $schema->string()->required(),
            'cid10' => $schema->string()->required(),
        ];
    }
}
