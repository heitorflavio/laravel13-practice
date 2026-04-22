<?php

namespace App\Livewire\Documents;

use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Jobs\SummarizeDocument;
use App\Jobs\SummarizeFile;
use App\Models\Document;
use App\Models\File;

#[Title('Edit document')]
class EditDocuments extends Component
{
    use WithFileUploads;

    public $id;
    public $title;
    public $content;

    public Document $document;

    public $files = [];

    public function mount($id)
    {
        $document = auth()->user()->documents()->findOrFail($id);

        $this->document = $document;
        $this->id = $document->id;
        $this->title = $document->name;
        $this->content = $document->content ?? '';
    }

    public function save()
    {
        try {
            DB::beginTransaction();
            $document = auth()->user()->documents()->findOrFail($this->id);
            $document->update([
                'name' => $this->title,
                'content' => $this->content,
            ]);
            DB::commit();
            Flux::toast(variant: 'success', text: __('Document updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast(variant: 'danger', text: __('Error updating document: ') . $e->getMessage());
        }
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function saveFiles()
    {
        $this->validate([
            'files.*' => 'file|max:10240' // 10MB
        ]);

        try {
            DB::beginTransaction();

            foreach ($this->files as $file) {

                $path = $file->store('documents', 'public');

                $fileRecord = File::create([
                    'document_id' => $this->document->id,
                    'file_path' => $path,
                    'file_url' => Storage::disk('public')->url($path),
                    'mime_type' => $file->getClientMimeType(),
                ]);

                SummarizeFile::dispatch($fileRecord);
            }

            DB::commit();

            // limpa input
            $this->files = [];

            // atualiza lista
            $this->document->refresh();

            Flux::toast(variant: 'success', text: 'Arquivos enviados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            Flux::toast(variant: 'danger', text: $e->getMessage());
        }
    }

    public function deleteFile($id)
    {
        $file = File::findOrFail($id);

        Storage::disk('public')->delete($file->path);

        $file->delete();

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

    public function summarizeDocument()
    {
        $hasSummaries = $this->document->files()->whereNotNull('ia_resume')->exists();

        if (! $hasSummaries) {
            Flux::toast(variant: 'warning', text: 'Nenhum arquivo com resumo disponível ainda.');
            return;
        }

        SummarizeDocument::dispatch($this->document);

        Flux::toast(variant: 'success', text: 'Resumo do documento sendo gerado em segundo plano.');
    }
}
