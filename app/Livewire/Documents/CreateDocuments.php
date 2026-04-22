<?php

namespace App\Livewire\Documents;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create document')]
class CreateDocument extends Component
{
    public string $title = '';

    public string $content = '';
}
