<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public ?string $title = null;

    public function render(): View
    {
        return view('components.layout');
    }
}
