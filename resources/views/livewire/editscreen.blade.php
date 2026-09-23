<?php

use Livewire\Component;
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

<div class="p-4 space-y-4 max-w-5xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')" icon="home">Dashboard</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('dashboard') . '#screens'">Screens</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $screen->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="w-full max-w-sm">
            <flux:input.group>
                <flux:input.group.prefix>Screen Name:</flux:input.group.prefix>
                <flux:input wire:model="name" placeholder="Screen Name" />
                <flux:button icon="check" wire:click="saveScreen">Save</flux:button>
            </flux:input.group>
        </div>
        <flux:button size="sm" variant="ghost" icon="tv" href="{{ route('viewer', ['screen' => $screen->id]) }}" target="_blank">View on TV</flux:button>
    </div>

    <div class="w-full max-w-md">
        <x-screen-preview :$screen />
    </div>

    <flux:card size="sm">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID</flux:table.column>
                <flux:table.column>Column Name</flux:table.column>
                <flux:table.column>Collection</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($screen->columns as $column)
                    <flux:table.row :key="$column->id">
                        <flux:table.cell>{{ $column->id }}</flux:table.cell>
                        <flux:table.cell variant="strong">
                            @if ($editingColumnId === $column->id)
                                <div class="flex items-center gap-2">
                                    <flux:input wire:model="columnName" size="sm" />
                                    <flux:button size="sm" variant="primary" wire:click="saveColumnName">Save</flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="cancelEditing">Cancel</flux:button>
                                </div>
                            @else
                                {{ $column->name }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($editingCollectionId && $columnCollectionId === $column->id)
                                <div class="flex items-center gap-2">
                                    <flux:select wire:model="selectedCollectionId" size="sm">
                                        <option value="">None</option>
                                        @foreach ($collections as $collection)
                                            <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:button size="sm" variant="primary" wire:click="saveColumnCollection">Save</flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="cancelEditingCollection">Cancel</flux:button>
                                </div>
                            @else
                                {{ $column->collection ? $column->collection->name : 'None' }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            @if ($editingColumnId !== $column->id && !($editingCollectionId && $columnCollectionId === $column->id))
                                <flux:button size="sm" variant="ghost" wire:click="startEditing({{ $column->id }})">Rename</flux:button>
                                <flux:button size="sm" variant="ghost" wire:click="startEditingCollection({{ $column->id }})">Change Collection</flux:button>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="deleteColumn({{ $column->id }})" wire:confirm="Are you sure you want to delete this column?" />
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach

                <flux:table.row>
                    <flux:table.cell colspan="4">
                        @if ($showAddForm)
                            <div class="flex items-center gap-2">
                                <flux:input wire:model="newColumnName" placeholder="Enter column name" class="flex-1" />
                                <flux:select wire:model="selectedCollectionId" placeholder="Collection (optional)" class="w-56">
                                    <option value="">None</option>
                                    @foreach ($collections as $collection)
                                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:button variant="primary" wire:click="addColumn">Add</flux:button>
                                <flux:button variant="ghost" wire:click="cancelAdd">Cancel</flux:button>
                            </div>
                            @error('newColumnName')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        @else
                            <flux:button variant="primary" icon="plus" wire:click="showAddColumnForm">Add New Column</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
