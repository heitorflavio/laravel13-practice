<?php

use App\Livewire\Dependents\ListDepedents;
use App\Models\User;
use Livewire\Livewire;

test('unauthenticated user is redirected', function () {
    $this->get(route('dependents.index'))->assertRedirect(route('login'));
});

test('authenticated user can render the component', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ListDepedents::class)
        ->assertOk();
});

test('deletes own dependent', function () {
    $user = User::factory()->create();
    $dependent = User::factory()->create(['parent_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ListDepedents::class)
        ->call('delete', $dependent->id);

    $this->assertDatabaseMissing('users', ['id' => $dependent->id]);
});

test('cannot delete dependent belonging to another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $dependent = User::factory()->create(['parent_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(ListDepedents::class)
        ->call('delete', $dependent->id);

    $this->assertDatabaseHas('users', ['id' => $dependent->id]);
});
