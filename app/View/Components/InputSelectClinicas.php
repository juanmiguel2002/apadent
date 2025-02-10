<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class inputSelectClinicas extends Component
{
    public $name;

    public $options;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $name, $options, string $label)
    {
        //
        $this->name = $name;
        $this->options = $options;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-select-clinicas');
    }
}
