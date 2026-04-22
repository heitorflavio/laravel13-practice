<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /** @return array<string, int> */
    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();

        $documentIds = $user->documents()->pluck('id');

        $totalDocuments = $documentIds->count();
        $totalFiles = File::whereIn('document_id', $documentIds)->count();
        $totalDependents = $user->dependents()->count();
        $filesWithResume = File::whereIn('document_id', $documentIds)->where('status', 'done')->count();
        $filesPending = File::whereIn('document_id', $documentIds)->whereIn('status', ['pending', 'processing'])->count();
        $docsWithResume = $user->documents()->whereNotNull('ia_resume')->count();

        return compact(
            'totalDocuments',
            'totalFiles',
            'totalDependents',
            'filesWithResume',
            'filesPending',
            'docsWithResume',
        );
    }

    /** @return Collection<int, Document> */
    #[Computed]
    public function recentDocuments(): Collection
    {
        return auth()->user()
            ->documents()
            ->withCount('files')
            ->latest()
            ->limit(5)
            ->get();
    }

    /** @return Collection<int, User> */
    #[Computed]
    public function dependents(): Collection
    {
        return auth()->user()->dependents()->latest()->get();
    }
}
