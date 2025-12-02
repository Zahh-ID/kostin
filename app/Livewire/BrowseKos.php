<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;

class BrowseKos extends Component
{
    public $search = '';

    public function render()
    {
        $properties = Property::where('name', 'like', '%'.$this->search.'%')->get();

        return view('livewire.browse-kos', [
            'properties' => $properties,
        ]);
    }
}
