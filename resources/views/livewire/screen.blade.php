<?php

use Livewire\Component;
use App\Models\Screen;
use App\Models\Column;
use App\Models\Collection;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

new class extends Component {
    use WithFileUploads;
    public $screen;
    public $columns;
    #[Validate('required|string|max:255')]
    public $name;
    public $column_id;
    public $selectedCollectionId;
    #[Validate('nullable|string|max:255')]
    public $puff_count;
    #[Validate('nullable|string|max:255')]
    public $volume;
    #[Validate('nullable|image')]
    public $image;
    #[Validate('nullable|integer|min:32|max:80')]
    public $font_size=32;

    public function mount(Screen $screen)
    {
        $this->screen = $screen;
        $this->refreshColumns();
    }

    public function refreshColumns()
    {   
        unset($this->columns);
        $this->columns = Column::where('screen_id', $this->screen->id)->get();
        if ($this->columns->count() == 0) {
            Flux::modal('add-column')->show();
        }
    }

    public function backToScreens()
    {
        $this->dispatch('screen-reset');
    }

    public function deleteScreen()
    {
        $this->screen->delete();
        $this->dispatch('screen-reset');
    }

    public function addColumn()
    {
        Column::create([
            'name' => $this->name,
            'screen_id' => $this->screen->id,
        ]);
        $this->reset(['name']);
        Flux::modal('add-column')->close();
    }

    public function deleteColumn($column_id)
    {
        Column::destroy($column_id);
    }

    public function selectCollection($column_id, $collection_id)
    {
        Column::where('id', $column_id)
            ->update([
                'collection_id' => $collection_id,
            ]);
    }

    public function with()
    {
        return [
            'collections' => Collection::orderBy('name')->get(),
        ];
    }

    public function addCollectionModal($column_id)
    {
        $this->column_id = $column_id;
        Flux::modal('add-collection')->show();
    }

    public function addCollection()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('collection-images', 'public');
        }

        $collection = Collection::create([
            'name' => $this->name,
            'puff_count' => $this->puff_count,
            'volume' => $this->volume,
            'image' => $imagePath,
            'image_name' => $this->image?->getClientOriginalName(),
            'font_size' => $this->font_size,
        ]);

        $this->selectCollection($this->column_id, $collection->id);
        Flux::modal('add-collection')->close();
    }
};
?>

<!-- Selected Screen Display -->
<main class="flex w-full gap-8 p-4 h-dvh">
    <header
        class="group overflow-hidden transition-all duration-300 ease-in-out h-10 hover:h-16 bg-transparent hover:bg-gray-800 opacity-0 hover:opacity-100 fixed top-0 left-0 right-0 z-50">
        <div
            class="max-w-7xl mx-auto py-1 group-hover:py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center transition-all duration-300">
            <div
                class="bg-zinc-900 flex items-center gap-4">
                <h2 class="font-semibold text-xl text-white">
                    <flux:button variant="danger" wire:confirm="Are you sure you want to delete this screen?" wire:click="deleteScreen">Delete {{ $screen->name }} screen</flux:button>
                </h2>
            </div>
            <flux:modal.trigger name="add-column">
                <flux:button variant="primary" size="sm">Add Column</flux:button>
            </flux:modal.trigger>
            <a href="{{ route('viewer') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Back to Screens
            </a>
        </div>
    </header>

    <div class="w-full flex justify-center h-full gap-6">
        @foreach ($columns as $column)
            @if ($column->collection !== null)
                <livewire:collection :$column wire:key="collection-{{ $column->id }}" />
            @else
                <div class="flex-1 flex flex-col gap-2 mx-auto items-center justify-center">
                    <h2 class="text-2xl font-bold mb-6 text-center">Select A Collection</h2>
                    @foreach ($collections as $collection)
                        <flux:button wire:click="selectCollection({{ $column->id }}, {{ $collection->id }})">
                            {{ $collection->name }}</flux:button>
                    @endforeach
                    <h2 class="text-2xl font-bold mb-6 text-center">Add A Collection</h2>
                    <flux:button wire:click="addCollectionModal({{ $column->id }})">Add New Collection</flux:button>
                    <h2 class="text-2xl font-bold mb-6 text-center">Delete This Column</h2>
                    <flux:button wire:click="deleteColumn({{ $column->id }})">Delete Column {{$column->name}}</flux:button>
                </div>
            @endif
        @endforeach
        <flux:modal name="add-column" class="max-w-md">
            <form wire:submit.prevent="addColumn" class="space-y-4">
                <flux:heading size="lg">Add New Column</flux:heading>
                <flux:input wire:model="name" label="Column Name" description="Used to choose the column." />
                <div class="flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Close</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" wire:loading.attr="disabled">Submit</flux:button>
                </div>
            </form>
        </flux:modal>
        <flux:modal name="add-collection" class="max-w-md">
            <form wire:submit.prevent="addCollection" class="space-y-4">
                <flux:heading size="lg">Add New Collection</flux:heading>
                <flux:input wire:model="name" label="Collection Name"
                    description="Used to choose the collection. eg: Beast Mode Max" />
                <flux:input wire:model="puff_count" label="Puff Count"
                    description="Number of Puffs. eg: 25,000 Puffs" />
                <flux:input wire:model="volume" label="Volume" description="Volume of the device. eg: 20ml" />
                <flux:input wire:model="font_size" label="Font Size" description="Font size of the collection" />
                <flux:input wire:model="image" label="Image" description="Collection Header Image" type="file"
                    accept="image/*"  />
                @if($image)
                    <div class="mt-2">
                        <img src="{{ $image->temporaryUrl() }}" class="h-32 w-auto rounded-md" />
                    </div>
                @endif
                <div class="flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Close</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit">Submit</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</main>
