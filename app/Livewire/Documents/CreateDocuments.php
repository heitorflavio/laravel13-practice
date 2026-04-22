<?php

namespace App\Livewire\Documents;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Document;

#[Title('Create document')]
class CreateDocument extends Component
{
    public $title;
    public $content;
}
