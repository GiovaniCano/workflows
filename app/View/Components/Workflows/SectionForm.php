<?php

namespace App\View\Components\Workflows;

use App\Models\Section as ModelsSection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SectionForm extends Component
{
    public array $models = [
        'section' => \App\Models\Section::class,
        'wysiwyg' => \App\Models\Wysiwyg::class,
        'image' => \App\Models\Image::class,
    ];

    public int $type;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ModelsSection $section
    )
    {
        $this->type = $section->type; 
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.workflows.section-form');
    }
}
