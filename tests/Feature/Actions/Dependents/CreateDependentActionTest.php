<?php

use App\Actions\Dependents\CreateDependentAction;
use App\DTOs\Dependents\CreateDependentData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('creates a dependent user linked to the parent', function () {
    $parent = User::factory()->create();

    $dependent = (new CreateDependentAction)->handle(new CreateDependentData(
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        parentId: $parent->id,
    ));

    expect($dependent)->toBeInstanceOf(User::class)
        ->and($dependent->parent_id)->toBe($parent->id)
        ->and($dependent->name)->toBe('John Doe')
        ->and($dependent->email)->toBe('john@example.com');

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'parent_id' => $parent->id,
    ]);
});

test('hashes the dependent password', function () {
    $parent = User::factory()->create();
    $dependent = (new CreateDependentAction)->handle(new CreateDependentData(
        name: 'Jane',
        email: 'jane@example.com',
        password: 'secret123',
        parentId: $parent->id,
    ));

    expect(Hash::check('secret123', $dependent->password))->toBeTrue();
});
