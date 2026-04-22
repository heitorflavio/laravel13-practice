<?php

use App\Livewire\Dependents\CreateDepedents;
use App\Models\User;
use Livewire\Livewire;

test('unauthenticated user is redirected', function () {
    $this->get(route('dependents.create'))->assertRedirect(route('login'));
});

test('validates required fields', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateDepedents::class)
        ->call('create')
        ->assertHasErrors(['name', 'email', 'password']);
});

test('validates email is unique', function () {
    $existing = User::factory()->create();
    $user     = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateDepedents::class)
        ->set('name', 'John')
        ->set('email', $existing->email)
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('create')
        ->assertHasErrors(['email']);
});

test('validates password confirmation', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateDepedents::class)
        ->set('name', 'John')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different')
        ->call('create')
        ->assertHasErrors(['password']);
});

test('creates dependent and redirects on valid data', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateDepedents::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('create')
        ->assertRedirect(route('dependents.index'));

    $this->assertDatabaseHas('users', [
        'email'     => 'john@example.com',
        'parent_id' => $user->id,
    ]);
});
