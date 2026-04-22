<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Dependents\ListDepedents;
use App\Livewire\Dependents\CreateDepedents;
use App\Livewire\Documents\CreateDocument;
use App\Livewire\Documents\ListDocuments;
use App\Livewire\Documents\EditDocuments;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('dependents/list', ListDepedents::class)->name('dependents.index');
    Route::livewire('dependents/create', CreateDepedents::class)->name('dependents.create');

    Route::livewire('documents/list', ListDocuments::class)->name('documents.index');
    Route::livewire('documents/create', CreateDocument::class)->name('documents.create');
    Route::livewire('documents/edit/{id}', EditDocuments::class)->name('documents.edit');
});

require __DIR__.'/settings.php';
