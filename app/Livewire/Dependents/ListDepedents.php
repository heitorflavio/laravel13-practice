<?php

namespace App\Livewire\Dependents;

use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Flux\Flux;

#[Title('Dependents')]
class ListDepedents extends Component
{
    use WithPagination;

    #[Computed]
    public function dependents()
    {
        return auth()->user()
            ->dependents()
            ->paginate(12);
    }

    public function delete($dependentId)
    {
        try {
            DB::beginTransaction();
            $dependent = auth()->user()->dependents()->findOrFail($dependentId);
            $dependent->delete();
            DB::commit();
            Flux::toast(variant: 'success', text: __('Dependent deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast(variant: 'danger', text: __('Error deleting dependent: ') . $e->getMessage());
        }
    }
}
