<?php

use App\Models\Collection;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

new class extends Component {
    use WithFileUploads;

    public $collection;
    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('nullable|file|mimetypes:image/jpeg,image/png')]
    public $image;
    #[Validate('nullable|string|max:255')]
    public $puff_count;
    #[Validate('nullable|string|max:255')]
    public $volume;
    #[Validate('nullable|integer|min:10|max:80')]
    public $font_size;
    public $current_image;

    // For item management
    public $items = [];
    public $newItemName = '';
    public $editItemId = null;
    public $editItemName = '';

    public function mount(Collection $collection)
    {
        $this->collection = $collection;
        $this->name = $collection->name;
        $this->puff_count = $collection->puff_count;
        $this->volume = $collection->volume;
        $this->font_size = $collection->font_size;
        $this->current_image = $collection->image;

        $this->loadItems();
    }

    public function with()
    {
        return [
            'usedOn' => $this->collection->columns()->with('screen')->get()
                ->pluck('screen.name')->filter()->unique()->values(),
        ];
    }

    public function loadItems()
    {
        $this->items = $this->collection->items()->orderBy('name')->get();
    }

    public function saveCollection()
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'puff_count' => $this->puff_count,
            'volume' => $this->volume,
            'font_size' => $this->font_size,
        ];

        if ($this->image) {
            $this->collection->update([
                'image' => $this->image->store('collection-images', 'public'),
            ]);
            $this->current_image = $this->collection->image;
        }

        $this->collection->update($data);
        $this->collection->refresh();

        session()->flash('message', 'Collection updated successfully!');
    }

    public function addItem()
    {
        $this->validate([
            'newItemName' => 'required|string|min:1',
        ]);

        Item::create([
            'collection_id' => $this->collection->id,
            'name' => $this->newItemName,
            'active' => true,
        ]);

        $this->newItemName = '';
        $this->loadItems();
    }

    public function startEditItem($itemId)
    {
        $this->editItemId = $itemId;
        $item = Item::find($itemId);
        $this->editItemName = $item->name;
    }

    public function cancelEditItem()
    {
        $this->editItemId = null;
        $this->editItemName = '';
    }

    public function saveItem()
    {
        $this->validate([
            'editItemName' => 'required|string|min:1',
        ]);

        $item = Item::find($this->editItemId);
        $item->name = $this->editItemName;
        $item->save();

        $this->cancelEditItem();
        $this->loadItems();
    }

    public function toggleItemStatus($itemId)
    {
        $item = Item::find($itemId);
        $item->active = !$item->active;
        $item->save();

        $this->loadItems();
    }

    public function deleteItem($itemId)
    {
        Item::destroy($itemId);
        $this->loadItems();
    }

    public function deleteCollection()
    {
        if ($this->current_image) {
            Storage::disk('public')->delete($this->current_image);
        }
        $this->collection->delete();
        $this->redirectRoute('dashboard');
    }
}; ?>

<div class="p-4 space-y-4 max-w-5xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')" icon="home">Dashboard</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('dashboard') . '#collections'">Collections</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $collection->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <flux:heading size="xl">Edit Collection</flux:heading>
        <div class="flex flex-wrap gap-1">
            @forelse ($usedOn as $screenName)
                <flux:badge size="sm" color="zinc" icon="tv">On {{ $screenName }}</flux:badge>
            @empty
                <flux:badge size="sm">Not on any screen</flux:badge>
            @endforelse
        </div>
    </div>

    <!-- Collection Details Form -->
    <flux:card class="space-y-6">
        <flux:heading size="lg">Collection Details</flux:heading>

        @if (session('message'))
            <flux:callout variant="success" icon="check-circle" :text="session('message')" />
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <flux:input type="text" label="Name" wire:model="name" />
                <flux:input type="text" label="Puff Count" wire:model="puff_count" />
                <flux:input type="text" label="Volume" wire:model="volume" />
                <flux:input type="number" label="Font Size" wire:model="font_size" min="8" max="72" />
            </div>

            <div>
                <flux:input type="file" label="Header Image" wire:model="image" accept="image/*" />

                <div wire:loading wire:target="image" class="mt-2">
                    <flux:text size="sm">Uploading...</flux:text>
                </div>

                @if ($image)
                    <div class="mt-2">
                        <flux:text size="sm">Preview:</flux:text>
                        <img src="{{ $image->temporaryUrl() }}"
                            class="mt-1 h-32 object-contain bg-zinc-100 dark:bg-zinc-800 rounded-md">
                    </div>
                @elseif ($current_image)
                    <div class="mt-2">
                        <flux:text size="sm">Current Image:</flux:text>
                        <img src="{{ Storage::url($current_image) }}"
                            class="mt-1 h-32 object-contain bg-zinc-100 dark:bg-zinc-800 rounded-md">
                    </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <flux:button variant="primary" wire:click="saveCollection">Save Collection</flux:button>
            <flux:modal.trigger name="delete-collection">
                <flux:button variant="danger">Delete Collection</flux:button>
            </flux:modal.trigger>
        </div>
    </flux:card>

    <!-- Items Management -->
    <flux:card class="space-y-6">
        <flux:heading size="lg">Manage Items</flux:heading>

        <!-- Add New Item Form -->
        <flux:field>
            <div class="flex items-center gap-2">
                <flux:input type="text" wire:model="newItemName" placeholder="Enter new item name" class="flex-1" />
                <flux:button variant="primary" wire:click="addItem">Add Item</flux:button>
            </div>
            <flux:error name="newItemName" />
        </flux:field>

        <!-- Items Table -->
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($items as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell variant="strong">
                            @if ($editItemId === $item->id)
                                <div class="flex items-center gap-2">
                                    <flux:input type="text" wire:model="editItemName" size="sm" />
                                    <flux:button size="sm" variant="primary" wire:click="saveItem">Save</flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="cancelEditItem">Cancel</flux:button>
                                </div>
                            @else
                                {{ $item->name }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:toggle size="sm" icon="eye" color="green" :checked="$item->active" on:label="Active" off:label="Hidden"
                                wire:click="toggleItemStatus({{ $item->id }})" />
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            @if ($editItemId !== $item->id)
                                <flux:button size="sm" variant="ghost" wire:click="startEditItem({{ $item->id }})">Edit</flux:button>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="Delete item &quot;{{ $item->name }}&quot;?" />
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">
                            No items found. Add your first item above.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal name="delete-collection" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Collection</flux:heading>
                <flux:text class="mt-2">
                    Are you sure you wish to delete this collection?
                    @if ($usedOn->isNotEmpty())
                        It is currently used on: {{ $usedOn->implode(', ') }}. Those columns will show as empty.
                    @endif
                </flux:text>
            </div>
            <div class="flex">
                <flux:spacer />
                <flux:button variant="danger" wire:click="deleteCollection">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
