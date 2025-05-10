<?php

use App\Models\Column;
use App\Models\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    use WithFileUploads;

    public $collection;
    public $column;
    #[Validate('required|string|max:255')]
    public string $name;
    #[Validate('nullable|string|max:255')]
    public ?string $title;
    #[Validate('nullable|string|max:255')]
    public ?string $subtitle;
    #[Validate('nullable|file|mimetypes:image/jpeg,image/png')]
    public $image;
    #[Validate('nullable|string|max:255')]
    public ?string $puff_count;
    #[Validate('nullable|string|max:255')]
    public ?string $volume;
    #[Validate('nullable|integer')]
    public $font_size;
    #[Validate('nullable|integer')]
    public $collection_id;
    public $collections;
    public $showEditCollectionModal = false;

    public function mount(Column $column)
    {
        $this->init($column);
    }   

    public function init(Column $column){
        $this->column = $column;
        $this->collection = $column->collection;
        $this->collection_id = $this->collection->id;
        $this->name = $this->collection->name;
        $this->puff_count = $this->collection->puff_count;
        $this->volume = $this->collection->volume;
        $this->font_size = $this->collection->font_size;
        $this->collections = Collection::orderBy('name')->get();
    }

    public function updateCollection()
    {
        $this->validate();
        $this->collection->update([
            'name' => $this->name,
            'puff_count' => $this->puff_count,
            'volume' => $this->volume,
            'font_size' => $this->font_size,
        ]);
        if ($this->image) {
            $this->collection->update([
                'image' => $this->image->store('collection-images', 'public'),
            ]);
        }
        $this->collection->refresh();
        $this->font_size = $this->collection->font_size;
        $this->showEditCollectionModal = false;
    }

    public function increaseFontSize()
    {
        $this->font_size = $this->font_size < 80 ? ($this->font_size += 2) : $this->font_size;
        $this->collection->update([
            'font_size' => $this->font_size,
        ]);
    }
    public function decreaseFontSize()
    {
        $this->font_size = $this->font_size > 32 ? ($this->font_size -= 2) : $this->font_size;
        $this->collection->update([
            'font_size' => $this->font_size,
        ]);
    }
    public function deleteColumn($column_id)
    {
        Column::destroy($column_id);
        return redirect(request()->header('Referer'));
    }
    public function changeCollection()
    {
        $this->column->update([
            'collection_id' => $this->collection_id,
        ]);
        $this->init($this->column);
        $this->showEditCollectionModal = false;
    }
}; ?>

<div class="flex-1 h-full" hover:cursor-pointer @mouseover="showHover = true"
    @mouseleave="showHover = false" x-data="{ showEditCollectionModal: $wire.entangle('showEditCollectionModal'), showHover: false }">
    <div class="flex flex-col gap-4 text-center h-full">
        <img class="w-64 mx-auto" src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}"
            title="Click to edit collection" @click="showEditCollectionModal = true">
        <h2 class="text-4xl font-bold">{{ $collection->puff_count }}</h2>
        <h3 class="text-3xl font-bold">{{ $collection->volume }}</h3>
        <div class="flex-1">
            <livewire:items :$collection :$font_size />
        </div>
        <div x-show="showHover" class="gap-2 flex justify-center p-2 bg-green-800">
            <flux:button wire:confirm="Are you sure you want to delete this column?" class="bottom-0"
                wire:click="deleteColumn({{ $column->id }})">Delete Column {{ $column->name }}</flux:button>
            <flux:button @click="showEditCollectionModal = true">Edit Collection</flux:button>
            <flux:button wire:click="increaseFontSize">Increase Font Size</flux:button>
            <flux:button wire:click="decreaseFontSize">Decrease Font Size</flux:button>
        </div>

    </div>

    <!-- Collection Editing Modal -->
    <div x-show="showEditCollectionModal" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-lg shadow-lg max-w-md w-full p-6 flex flex-col gap-4"
            x-on:click.outside="showEditCollectionModal = false">
            <flux:select wire:model="collection_id" label="Change Collection" description="Select Collection">
                @foreach ($collections as $collection)
                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                @endforeach
            </flux:select>
            <flux:button wire:click="changeCollection">Change Collection</flux:button>
            <h3 class="text-xl font-bold text-white mb-4">Edit Collection</h3>
            <flux:input type="file" wire:model="image" label="Header Image"
                description="Select image to replace Column Header Image" />
            <!-- Image Preview -->
            @if ($image)
                <div class="mt-2">
                    <img src="{{ $image->temporaryUrl() }}" class="h-32 w-auto rounded-md" />
                </div>
            @endif
            <flux:input wire:model="name" label="Collection Name" description="eg: Beast Mode Max" />
            <flux:input wire:model="puff_count" label="Puff Count" description="Number of Puffs. eg: 25,000 Puffs" />
            <flux:input wire:model="volume" label="Volume" description="Volume of the device. eg: 20ml" />
            <flux:input type="number" wire:model="font_size" label="Font Size"
                description="Font size of the collection" />
            <flux:button wire:click="updateCollection">Submit</flux:button>
        </div>
    </div>
</div>
