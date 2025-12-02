<div>
    <input wire:model.live="search" type="text" placeholder="Search properties..." class="mb-4 p-2 border border-gray-300 rounded">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($properties as $property)
            <div class="border border-gray-200 rounded-lg shadow-sm">
                <div class="p-4">
                    <h3 class="font-bold text-lg">{{ $property->name }}</h3>
                    <p class="text-gray-600">{{ $property->address }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
