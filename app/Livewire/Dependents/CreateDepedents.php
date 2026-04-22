<?php

namespace App\Livewire\Dependents;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;

#[Title('Create dependents')]
class CreateDepedents extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'parent_id' => auth()->id(),
            ]);

            Flux::toast(variant: 'success', text: __('Dependent created successfully.'));
            DB::commit();

            return redirect()->route('dependents.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast(variant: 'danger', text: __('Error creating dependent: ') . $e->getMessage());
        }
    }
}
