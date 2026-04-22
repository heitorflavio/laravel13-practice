<?php

namespace App\Actions\Dependents;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteDependentAction
{
    public function handle(User $dependent): void
    {
        DB::transaction(fn () => $dependent->delete());
    }
}
