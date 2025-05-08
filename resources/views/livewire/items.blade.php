<?php

use Livewire\Volt\Component;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

new class extends Component {
    public $collection;
    public $items;
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
        $this->refreshItems();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->resetForm();
        $this->showEditModal = false;
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

<div class="relative" x-data="{ show: @entangle('showEditModal') }">
    <!-- Main item list display -->
    <div class="flex flex-col gap-4 text-[{{ $collection->font_size }}px] text-center" x-data="{ showHover: false }"
        @mouseover="showHover = true" @mouseleave="showHover = false" wire:poll.5s="refreshItems">
        @forelse($items->where('active', true) as $item)
            <p class="cursor-pointer hover:font-bold" wire:click="toggleActive({{ $item->id }})">{{ $item->name }}</p>
        @empty
            <p>No items found</p>
        @endforelse

        <!-- Hover edit button -->
        <div x-show="showHover" class="absolute top-0 right-0 p-2 bg-gray-800 bg-opacity-75 rounded-bl-lg cursor-pointer"
            wire:click="openEditModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    Edit Items
            </svg>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                class="inline-block align-bottom bg-gray-800 text-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full p-6"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Edit Items for {{ $collection->name }}</h3>
                    <button @click="show = false" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Add new item form -->
                <div class="mb-6 bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-xl font-semibold mb-3">Add New Item</h4>
                    <div class="flex gap-2">
                        <input type="text" wire:model="newItemName"
                            class="flex-1 px-3 py-2 bg-gray-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter item name">
                        <button wire:click="addItem"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            Add Item
                        </button>
                    </div>
                    @error('newItemName')
                        <span class="text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Edit item form (shows when editing) -->
                @if ($editItemId)
                    <div class="mb-6 bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-xl font-semibold mb-3">Edit Item</h4>
                        <div class="flex gap-2">
                            <input type="text" wire:model="editItemName"
                                class="flex-1 px-3 py-2 bg-gray-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter item name">
                            <button wire:click="updateItem"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                                Update
                            </button>
                            <button wire:click="resetForm"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                                Cancel
                            </button>
                        </div>
                        @error('editItemName')
                            <span class="text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-gray-700 rounded-lg overflow-hidden">
                        <thead class="bg-gray-600">
                            <tr>
                                <th class="py-3 px-4 text-left text-lg font-semibold">Name</th>
                                <th class="py-3 px-4 text-center text-lg font-semibold">Status</th>
                                <th class="py-3 px-4 text-right text-lg font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr class="border-t border-gray-600">
                                    <td class="py-3 px-4 text-lg">{{ $item->name }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $item->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex justify-end space-x-2">
                                            <button wire:click="editItem({{ $item->id }})"
                                                class="p-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="toggleActive({{ $item->id }})"
                                                class="p-1 {{ $item->active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded">
                                                @if ($item->active)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                            <button wire:click="deleteItem({{ $item->id }})"
                                                class="p-1 bg-red-600 text-white rounded hover:bg-red-700"
                                                onclick="confirm('Are you sure you want to delete this item?') || event.stopImmediatePropagation()">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-lg">No items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
