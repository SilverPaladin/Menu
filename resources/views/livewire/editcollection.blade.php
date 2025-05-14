<?php

use App\Models\Collection;
use App\Models\Item;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $collection;
    public $name;
    public $image;
    public $puff_count;
    public $volume;
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
    
    public function loadItems()
    {
        $this->items = $this->collection->items()->orderBy('name')->get();
    }
    
    public function saveCollection()
    {
        $this->validate([
            'name' => 'required|string|min:1',
            'puff_count' => 'nullable|string',
            'volume' => 'nullable|string',
            'font_size' => 'nullable|integer|min:8|max:72',
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);
        
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
            'newItemName' => 'required|string|min:1'
        ]);
        
        Item::create([
            'collection_id' => $this->collection->id,
            'name' => $this->newItemName,
            'active' => true
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
            'editItemName' => 'required|string|min:1'
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
}; ?>

<div class="p-4">
    <h2 class="text-2xl font-bold mb-6">Edit Collection</h2>
    
    <!-- Collection Details Form -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium mb-4 dark:text-white">Collection Details</h3>
        
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <flux:input type="text" label="Name" wire:model="name" />
                </div>
                
                <div class="mb-4">
                    <flux:input type="text" label="Puff Count" wire:model="puff_count" />
                </div>
                
                <div class="mb-4">
                    <flux:input type="text" label="Volume" wire:model="volume" />
                </div>
                
                <div class="mb-4">
                    <flux:input type="number" label="Font Size" wire:model="font_size" min="8" max="72" />
                </div>
            </div>
            
            <div>
                <div class="mb-4">
                    <flux:input type="file" label="Header Image" wire:model="image" accept="image/*" />
                    
                    <div wire:loading wire:target="image" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Uploading...
                    </div>
                    
                    @if ($image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Preview:</p>
                            <img src="{{ $image->temporaryUrl() }}" class="mt-1 h-32 object-contain bg-gray-100 dark:bg-gray-700 rounded-md">
                        </div>
                    @elseif ($current_image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Current Image:</p>
                            <img src="{{ Storage::url($current_image) }}" class="mt-1 h-32 object-contain bg-gray-100 dark:bg-gray-700 rounded-md">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-4">
            <flux:button variant="primary" wire:click="saveCollection">Save Collection</flux:button>
        </div>
    </div>
    
    <!-- Items Management -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h3 class="text-lg font-medium mb-4 dark:text-white">Manage Items</h3>
        
        <!-- Add New Item Form -->
        <div class="mb-6">
            <div class="flex items-center">
                <flux:input type="text" wire:model="newItemName" placeholder="Enter new item name" />
                <flux:button variant="primary" wire:click="addItem" class="ml-2">Add Item</flux:button>
            </div>
            @error('newItemName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                @if ($editItemId === $item->id)
                                    <div class="flex items-center">
                                        <flux:input type="text" wire:model="editItemName" />
                                        <flux:button variant="primary" wire:click="saveItem" class="ml-2">Save</flux:button>
                                        <flux:button variant="ghost" wire:click="cancelEditItem" class="ml-2">Cancel</flux:button>
                                    </div>
                                @else
                                    {{ $item->name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <flux:button variant="{{ $item->active ? 'primary' : 'subtle' }}" wire:click="toggleItemStatus({{ $item->id }})">
                                    {{ $item->active ? 'Active' : 'Hidden' }}
                                </flux:button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($editItemId !== $item->id)
                                    <flux:button variant="ghost" wire:click="startEditItem({{ $item->id }})" class="mr-3">Edit</flux:button>
                                    <flux:button variant="danger" wire:click="deleteItem({{ $item->id }})" onclick="return confirm('Are you sure you want to delete this item?')">Delete</flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                No items found. Add your first item above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        <flux:button variant="ghost" href="{{ route('dashboard') }}">Back to Dashboard</flux:button>
    </div>
</div>
