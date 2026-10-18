<?php

namespace App\View\Components;

use App\Models\Project;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public ?Project $project;

    /**
     * Create a new component instance.
     */
    public function __construct(?Project $project = null)
    {
        $this->project = $project;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
