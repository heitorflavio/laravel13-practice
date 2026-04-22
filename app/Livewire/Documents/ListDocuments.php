<?php

namespace App\Livewire\Documents;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Document;
use Livewire\Attributes\Computed;

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

    public function delete($documentId)
    {
        try {
            DB::beginTransaction();
            $document = auth()->user()->documents()->findOrFail($documentId);
            $document->delete();
            DB::commit();
            Flux::toast(variant: 'success', text: __('Document deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast(variant: 'danger', text: __('Error deleting document: ') . $e->getMessage());
        }
    }

    public function create()
    {
        $count = auth()->user()->documents()->count();

        Document::create([
            'user_id' => auth()->id(),
            'name' => 'New Document ' . ($count + 1),
        ]);
    }
}
