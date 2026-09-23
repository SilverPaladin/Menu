<?php

use App\Models\Column;
use App\Models\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\On;
use Livewire\Component;

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
    #[Validate('nullable|integer|min:10|max:80')]
    public $font_size;
    #[Validate('nullable|integer')]
    public $collection_id;
    public $collections;

    public function mount(Column $column)
    {
        $this->init($column);
    }

    public function init(Column $column)
    {
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
        Flux::modal('edit-collection-' . $this->column->id)->close();
    }

    public function increaseFontSize()
    {
        $this->font_size = $this->font_size < 80 ? ++$this->font_size : $this->font_size;
        $this->collection->update([
            'font_size' => $this->font_size,
        ]);
    }
    public function decreaseFontSize()
    {
        $this->font_size = $this->font_size > 9 ? --$this->font_size : $this->font_size;
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
        Flux::modal('edit-collection-' . $this->column->id)->close();
    }
}; ?>

<div class="flex-1 h-full" x-data="{ showHover: false }">
    <div class="flex flex-col gap-4 text-center h-full">
        <flux:modal.trigger name="edit-collection-{{ $column->id }}">
            <img class="w-64 mx-auto" src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}"
                title="hover on bottom screen to show options">
        </flux:modal.trigger>
        <h2 class="text-4xl font-bold">{{ $collection->puff_count }}</h2>
        <h3 class="text-3xl font-bold">{!! $collection->volume !!}</h3>
        <div class="flex-1">
            <livewire:items :$collection :$font_size wire:key="items-{{ $collection->id }}" />
        </div>
        <div class="h-10" hover:cursor-pointer @mouseover="showHover = true" @mouseleave="showHover = false">
            <div x-show="showHover" class="gap-2 flex justify-center p-2 bg-green-800">
                <flux:button wire:confirm="Are you sure you want to delete this column?" class="bottom-0"
                    wire:click="deleteColumn({{ $column->id }})">Delete Column {{ $column->name }}</flux:button>
                <flux:modal.trigger name="edit-collection-{{ $column->id }}">
                    <flux:button>Edit Collection</flux:button>
                </flux:modal.trigger>
                <flux:button wire:click="decreaseFontSize">Decrease Font Size</flux:button>
                <flux:button wire:click="increaseFontSize">Increase Font Size</flux:button>
            </div>
        </div>
    </div>

    <!-- Collection Editing Modal -->
    <flux:modal name="edit-collection-{{ $column->id }}" class="md:w-96">
        <div class="space-y-6">
            <flux:heading size="lg">Edit Collection</flux:heading>

            <flux:select wire:model="collection_id" label="Change Collection" description="Select Collection">
                @foreach ($collections as $collection)
                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                @endforeach
            </flux:select>
            <flux:button wire:click="changeCollection">Change Collection</flux:button>

            <flux:separator />

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
                description="Font size of the collection." />
            <div class="flex">
                <flux:spacer />
                <flux:button variant="primary" wire:click="updateCollection">Submit</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
