<?php

namespace App\Livewire\Documents;

use App\Actions\Documents\DeleteFileAction;
use App\Actions\Documents\SaveFilesAction;
use App\Actions\Documents\SummarizeDocumentAction;
use App\Actions\Documents\UpdateDocumentAction;
use App\DTOs\Documents\SaveFilesData;
use App\DTOs\Documents\UpdateDocumentData;
use App\Models\Document;
use App\Models\File;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Edit document')]
class EditDocuments extends Component
{
    use WithFileUploads;

    public int $id;
    public string $title;
    public string $content;
    public Document $document;
    public array $files = [];

    public function mount(int $id): void
    {
        $document = auth()->user()->documents()->findOrFail($id);

        $this->document = $document;
        $this->id       = $document->id;
        $this->title    = $document->name;
        $this->content  = $document->content ?? '';
    }

    public function save(UpdateDocumentAction $action): void
    {
        try {
            $document = auth()->user()->documents()->findOrFail($this->id);
            $action->handle($document, new UpdateDocumentData(
                name: $this->title,
                content: $this->content,
            ));
            Flux::toast(variant: 'success', text: __('Document updated successfully.'));
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error updating document: ') . $e->getMessage());
        }
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function saveFiles(SaveFilesAction $action): void
    {
        $this->validate([
            'files.*' => 'file|max:10240',
        ]);

        try {
            $action->handle(new SaveFilesData(
                documentId: $this->document->id,
                files: $this->files,
            ));

            $this->files = [];
            $this->document->refresh();

            Flux::toast(variant: 'success', text: 'Arquivos enviados com sucesso!');
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: $e->getMessage());
        }
    }

    public function deleteFile(int $id, DeleteFileAction $action): void
    {
        $file = File::findOrFail($id);
        $action->handle($file);
        $this->document->refresh();
        Flux::toast(variant: 'success', text: 'Arquivo removido!');
    }

    #[Computed]
    public function hasPendingWork(): bool
    {
        return $this->document->files()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }

    public function refreshData(): void
    {
        $this->document->refresh();
        unset($this->hasPendingWork);
        $this->title = $this->document->name;
    }

    public function summarizeDocument(SummarizeDocumentAction $action): void
    {
        if (! $this->document->files()->whereNotNull('ia_resume')->exists()) {
            Flux::toast(variant: 'warning', text: 'Nenhum arquivo com resumo disponível ainda.');
            return;
        }

        $action->handle($this->document);

        Flux::toast(variant: 'success', text: 'Resumo do documento sendo gerado em segundo plano.');
    }
}
