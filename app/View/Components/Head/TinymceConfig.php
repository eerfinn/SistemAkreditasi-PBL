<?php

namespace App\View\Components\Head;

use Illuminate\View\Component;

class TinymceConfig extends Component
{
    /**
     * ID dari textarea yang akan diubah menjadi TinyMCE editor
     *
     * @var string
     */
    public $selector;

    /**
     * Tinggi editor dalam piksel
     *
     * @var int
     */
    public $height;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selector = 'textarea', $height = 500)
    {
        $this->selector = $selector;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.head.tinymce-config');
    }
}
