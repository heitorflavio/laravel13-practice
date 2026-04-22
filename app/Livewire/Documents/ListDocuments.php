<?php

namespace App\Livewire\Documents;

use App\Actions\Documents\CreateDocumentAction;
use App\Actions\Documents\DeleteDocumentAction;
use App\DTOs\Documents\CreateDocumentData;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('List documents')]
class ListDocuments extends Component
{
    #[Computed]
    public function documents()
    {
        return auth()->user()
            ->documents()
            ->withCount('files')
            ->paginate(10);
    }

    public function delete(int $documentId, DeleteDocumentAction $action): void
    {
        try {
            $document = auth()->user()->documents()->findOrFail($documentId);
            $action->handle($document);
            Flux::toast(variant: 'success', text: __('Document deleted successfully.'));
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error deleting document: ') . $e->getMessage());
        }
    }

    public function create(CreateDocumentAction $action): void
    {
        $action->handle(new CreateDocumentData(
            userId: auth()->id(),
        ));
    }
}
