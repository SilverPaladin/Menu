<?php

use Livewire\Volt\Component;
use App\Models\Screen;
use App\Models\Column;
use App\Models\Collection;

new class extends Component {
    public $screen;
    public $name;
    public $editingColumnId = null;
    public $columnName = '';
    public $showAddForm = false;
    public $newColumnName = '';
    public $collections = [];
    public $selectedCollectionId = null;
    public $editingCollectionId = false;
    public $columnCollectionId = null;

    public function mount(Screen $screen)
    {
        $screen->load('columns.collection');
        $this->screen = $screen;
        $this->name = $screen->name;
        $this->collections = Collection::orderBy('name')->get();
    }

    public function startEditing($columnId)
    {
        $this->editingColumnId = $columnId;
        $column = Column::find($columnId);
        $this->columnName = $column->name;
    }

    public function cancelEditing()
    {
        $this->editingColumnId = null;
        $this->columnName = '';
    }

    public function saveColumnName()
    {
        $column = Column::find($this->editingColumnId);
        $column->name = $this->columnName;
        $column->save();
        
        $this->screen->refresh();
        $this->cancelEditing();
    }

    public function startEditingCollection($columnId)
    {
        $this->editingCollectionId = true;
        $column = Column::find($columnId);
        $this->selectedCollectionId = $column->collection_id;
        $this->columnCollectionId = $columnId;
    }

    public function cancelEditingCollection()
    {
        $this->editingCollectionId = false;
        $this->selectedCollectionId = null;
        $this->columnCollectionId = null;
    }

    public function saveColumnCollection()
    {
        $column = Column::find($this->columnCollectionId);
        $column->collection_id = $this->selectedCollectionId;
        $column->save();
        
        $this->screen->refresh();
        $this->cancelEditingCollection();
    }

    public function deleteColumn($columnId)
    {
        Column::destroy($columnId);
        $this->screen->refresh();
    }

    public function showAddColumnForm()
    {
        $this->showAddForm = true;
        $this->selectedCollectionId = null;
    }

    public function cancelAdd()
    {
        $this->showAddForm = false;
        $this->newColumnName = '';
        $this->selectedCollectionId = null;
    }

    public function addColumn()
    {
        $this->validate([
            'newColumnName' => 'required|min:1'
        ]);

        Column::create([
            'name' => $this->newColumnName,
            'screen_id' => $this->screen->id,
            'collection_id' => $this->selectedCollectionId
        ]);

        $this->screen->refresh();
        $this->cancelAdd();
    }
    public function saveScreen(){
        $this->screen->update([
            'name' => $this->name
        ]);
        $this->screen->refresh();
    }
};
?>

<div class="p-4">
    <div class="w-1/4 mb-4">
    <flux:input.group>
        <flux:input.group.prefix>Screen Name:</flux:input.group.prefix>
        <flux:input wire:model="name" placeholder="Screen Name" />
        <flux:button icon="plus" wire:click="saveScreen">Save</flux:button>
    </flux:input.group>
    </div>    
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Column Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Collection</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($screen->columns as $column)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $column->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        @if ($editingColumnId === $column->id)
                            <div class="flex items-center">
                                <input type="text" wire:model="columnName" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full" />
                                <button wire:click="saveColumnName" class="ml-2 inline-flex items-center px-2 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Save
                                </button>
                                <button wire:click="cancelEditing" class="ml-2 inline-flex items-center px-2 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </button>
                            </div>
                        @else
                            {{ $column->name }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if ($editingCollectionId && $columnCollectionId === $column->id)
                            <div class="flex items-center">
                                <select wire:model="selectedCollectionId" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">
                                    <option value="">None</option>
                                    @foreach ($collections as $collection)
                                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                    @endforeach
                                </select>
                                <button wire:click="saveColumnCollection" class="ml-2 inline-flex items-center px-2 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Save
                                </button>
                                <button wire:click="cancelEditingCollection" class="ml-2 inline-flex items-center px-2 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </button>
                            </div>
                        @else
                            {{ $column->collection ? $column->collection->name : 'None' }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if ($editingColumnId !== $column->id && !($editingCollectionId && $columnCollectionId === $column->id))
                            <button wire:click="startEditing({{ $column->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                Rename
                            </button>
                            <button wire:click="startEditingCollection({{ $column->id }})" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3">
                                Change Collection
                            </button>
                            <button wire:click="deleteColumn({{ $column->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="return confirm('Are you sure you want to delete this column?')">
                                Delete
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
                
                <!-- Add Column Row -->
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <td colspan="4" class="px-6 py-4">
                        @if ($showAddForm)
                            <div class="flex items-center">
                                <input type="text" wire:model="newColumnName" placeholder="Enter column name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full" />
                                <button wire:click="addColumn" class="ml-2 inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Add
                                </button>
                                <button wire:click="cancelAdd" class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </button>
                            </div>
                        @else
                            <button wire:click="showAddColumnForm" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Column
                            </button>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Back to Dashboard
        </a>
    </div>
</div>
