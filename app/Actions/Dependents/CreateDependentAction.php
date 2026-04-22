<?php

namespace App\Actions\Dependents;

use App\DTOs\Dependents\CreateDependentData;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateDependentAction
{
    public function handle(CreateDependentData $data): User
    {
        return DB::transaction(fn () => User::create([
            'name'      => $data->name,
            'email'     => $data->email,
            'password'  => bcrypt($data->password),
            'parent_id' => $data->parentId,
        ]));
    }
}
