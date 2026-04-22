<?php

namespace App\Livewire\Dependents;

use App\Actions\Dependents\DeleteDependentAction;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Dependents')]
class ListDepedents extends Component
{
    use WithPagination;

    /** @return \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\User> */
    #[Computed]
    public function dependents(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return auth()->user()
            ->dependents()
            ->paginate(12);
    }

    public function delete(int $dependentId, DeleteDependentAction $action): void
    {
        try {
            $dependent = auth()->user()->dependents()->findOrFail($dependentId);
            $action->handle($dependent);
            Flux::toast(variant: 'success', text: __('Dependent deleted successfully.'));
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error deleting dependent: ') . $e->getMessage());
        }
    }
}
