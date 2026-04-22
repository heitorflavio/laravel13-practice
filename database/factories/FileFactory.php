<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'file_path'   => 'documents/' . fake()->uuid() . '.pdf',
            'file_url'    => 'http://localhost/storage/documents/test.pdf',
            'mime_type'   => 'application/pdf',
            'status'      => 'pending',
        ];
    }

    public function done(): static
    {
        return $this->state([
            'status'    => 'done',
            'ia_resume' => fake()->paragraph(),
        ]);
    }

    public function processing(): static
    {
        return $this->state(['status' => 'processing']);
    }
}
