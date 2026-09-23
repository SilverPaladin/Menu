<?php

use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    public $collection;
    public $items;
    #[Reactive]
    public $font_size = 30;
    public $showEditModal = false;
    public $newItemName = '';
    public $editItemId = null;
    public $editItemName = '';

    // Validation rules
    protected $rules = [
        'newItemName' => 'required|min:2|max:255',
        'editItemName' => 'required|min:2|max:255',
    ];

    public function mount(Collection $collection, $font_size)
    {
        $this->collection = $collection;
        $this->refreshItems();
    }

    public function refreshItems()
    {
        $this->items = Item::orderBy('name')->where('collection_id', $this->collection->id)->get();
    }

    public function openEditModal()
    {
        Flux::modal('edit-items')->show();
    }

    public function closeEditModal()
    {
        $this->resetForm();
        Flux::modal('edit-items')->close();
    }

    public function resetForm()
    {
        $this->newItemName = '';
        $this->editItemId = null;
        $this->editItemName = '';
        $this->resetValidation();
    }

    public function addItem()
    {
        $this->validate([
            'newItemName' => 'required|min:2|max:255',
        ]);

        Item::create([
            'collection_id' => $this->collection->id,
            'name' => $this->newItemName,
            'active' => true,
        ]);

        $this->refreshItems();
        $this->newItemName = '';
    }

    public function editItem($id)
    {
        $item = Item::find($id);
        if ($item) {
            $this->editItemId = $item->id;
            $this->editItemName = $item->name;
        }
    }

    public function updateItem()
    {
        if (!$this->editItemId) {
            return;
        }

        $this->validate([
            'editItemName' => 'required|min:2|max:255',
        ]);

        $item = Item::find($this->editItemId);
        if ($item) {
            $item->update([
                'name' => $this->editItemName,
            ]);

            $this->refreshItems();
            $this->resetForm();
        }
    }

    public function toggleActive($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->update([
                'active' => !$item->active,
            ]);

            $this->refreshItems();
        }
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->delete();
            $this->refreshItems();
        }
    }
};
?>

<div class="relative">
    <!-- Main item list display -->
    <div class="flex flex-col gap-1 text-[{{ $collection->font_size }}px] text-center" x-data="{ showHover: false }"
        @mouseover="showHover = true" @mouseleave="showHover = false">
        @forelse($items->where('active', true) as $item)
            <p class="cursor-pointer hover:font-bold" wire:confirm="Are you sure you want to hide this item?"
                wire:click="toggleActive({{ $item->id }})">{{ $item->name }}</p>
        @empty
            <p class="text-red-500 text-2xl font-bold">Out of Stock</p>
        @endforelse
        <!-- Hover edit button -->
        <flux:modal.trigger name="edit-items-{{ $collection->id }}" x-show="showHover">
            <flux:button variant="primary">Edit Items</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:modal name="edit-items-{{ $collection->id }}" class="max-w-4xl">
        <div class="space-y-6">
            <flux:heading>Edit Items for {{ $collection->name }}</flux:heading>

            <!-- Add new item form -->
            <flux:field>
                <div class="flex items-center gap-2">
                    <flux:input wire:model="newItemName" placeholder="Enter item name" class="flex-1" />
                    <flux:button variant="primary" wire:click="addItem">Add Item</flux:button>
                </div>
                <flux:error name="newItemName" />
            </flux:field>

            <!-- Items Table -->
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>

                    <flux:table.column align="end">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($items as $item)
                        <flux:table.row :key="$item->id">
                            @if ($editItemId === $item->id)
                                <flux:table.cell variant="strong" colspan="2">
                                    <div class="flex items-center gap-2">
                                        <flux:input wire:model="editItemName" size="sm" />
                                        <flux:button size="sm" variant="primary" wire:click="updateItem">Save
                                        </flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="resetForm">Cancel
                                        </flux:button>
                                    </div>
                                    <flux:error name="editItemName" />
                                </flux:table.cell>
                            @else
                                <flux:table.cell variant="strong">
                                    {{ $item->name }}
                                </flux:table.cell>
                            @endif

                            <flux:table.cell align="end">
                                @if ($editItemId !== $item->id)
                                    <flux:toggle size="sm" icon="eye" color="green" :checked="$item->active"
                                        on:label="Active" off:label="Hidden"
                                        wire:click="toggleActive({{ $item->id }})" />
                                    <flux:button size="sm" variant="ghost"
                                        wire:click="editItem({{ $item->id }})">Edit</flux:button>
                                    <flux:button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteItem({{ $item->id }})"
                                        wire:confirm="Delete item &quot;{{ $item->name }}&quot;?" />
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="2" class="text-center">No items found</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:modal>
</div>
