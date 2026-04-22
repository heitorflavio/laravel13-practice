<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\File;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /** @return array<string, int> */
    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();

        $documentIds = $user->documents()->pluck('id');

        $totalDocuments  = $documentIds->count();
        $totalFiles      = File::whereIn('document_id', $documentIds)->count();
        $totalDependents = $user->dependents()->count();
        $filesWithResume = File::whereIn('document_id', $documentIds)->where('status', 'done')->count();
        $filesPending    = File::whereIn('document_id', $documentIds)->whereIn('status', ['pending', 'processing'])->count();
        $docsWithResume  = $user->documents()->whereNotNull('ia_resume')->count();

        return compact(
            'totalDocuments',
            'totalFiles',
            'totalDependents',
            'filesWithResume',
            'filesPending',
            'docsWithResume',
        );
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> */
    #[Computed]
    public function recentDocuments(): \Illuminate\Database\Eloquent\Collection
    {
        return auth()->user()
            ->documents()
            ->withCount('files')
            ->latest()
            ->limit(5)
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> */
    #[Computed]
    public function dependents(): \Illuminate\Database\Eloquent\Collection
    {
        return auth()->user()->dependents()->latest()->get();
    }
}
