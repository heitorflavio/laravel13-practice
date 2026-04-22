<?php

use App\Actions\Dependents\DeleteDependentAction;
use App\Models\User;

test('deletes the dependent user', function () {
    $parent = User::factory()->create();
    $dependent = User::factory()->create(['parent_id' => $parent->id]);

    (new DeleteDependentAction)->handle($dependent);

    $this->assertDatabaseMissing('users', ['id' => $dependent->id]);
});

test('does not delete the parent when deleting a dependent', function () {
    $parent = User::factory()->create();
    $dependent = User::factory()->create(['parent_id' => $parent->id]);

    (new DeleteDependentAction)->handle($dependent);

    $this->assertDatabaseHas('users', ['id' => $parent->id]);
});
