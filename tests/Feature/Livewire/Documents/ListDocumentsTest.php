<?php

use App\Livewire\Documents\ListDocuments;
use App\Models\Document;
use App\Models\User;
use Livewire\Livewire;

test('unauthenticated user is redirected', function () {
    $this->get(route('documents.index'))->assertRedirect(route('login'));
});

test('authenticated user can render the component', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ListDocuments::class)
        ->assertOk();
});

test('creates a new document for the authenticated user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ListDocuments::class)
        ->call('create');

    expect($user->documents()->count())->toBe(1);
});

test('deletes own document', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ListDocuments::class)
        ->call('delete', $document->id);

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('cannot delete another user document', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(ListDocuments::class)
        ->call('delete', $document->id);

    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});
