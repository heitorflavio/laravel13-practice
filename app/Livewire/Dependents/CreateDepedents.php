<?php

namespace App\Livewire\Dependents;

use App\Actions\Dependents\CreateDependentAction;
use App\DTOs\Dependents\CreateDependentData;
use Flux\Flux;
use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

#[Title('Create dependents')]
class CreateDepedents extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function create(CreateDependentAction $action): Redirector|RedirectResponse
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $action->handle(new CreateDependentData(
                name: $this->name,
                email: $this->email,
                password: $this->password,
                parentId: auth()->id(),
            ));

            Flux::toast(variant: 'success', text: __('Dependent created successfully.'));
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error creating dependent: ').$e->getMessage());
        }

        return redirect()->route('dependents.index');
    }
}
