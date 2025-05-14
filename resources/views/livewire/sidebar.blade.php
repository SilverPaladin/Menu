<?php

use Livewire\Volt\Component;
use App\Models\Screen;
use App\Models\Collection;
use Livewire\Attributes\Validate;

new class extends Component {
    public $screens;
    public $collections;
    #[Validate('required|string|max:255')]
    public $name;
    public function mount()
    {
        $this->screens = Screen::orderBy('name')->get();
        $this->collections = Collection::orderBy('name')->get();
    }
    public function addScreen()
    {
        $this->validate();
        Screen::create([
            'name' => $this->name,
        ]);
        $this->reset(['name']);
        $this->screens = Screen::orderBy('name')->get();
    }
    public function addCollection()
    {
        $this->validate();
        Collection::create([
            'name' => $this->name,
        ]);
        $this->reset(['name']);
        $this->collections = Collection::orderBy('name')->get();
    }
}; ?>

<div>
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Screens')" expanded expandable>
            @foreach ($screens as $screen)
                <flux:navlist.item :href="route('screens', $screen->id)" :current="request()->routeIs('screens', $screen->id)">Screen: {{ $screen->name }}</flux:navlist.item>
            @endforeach
            <flux:modal.trigger name="add-screen">
                <flux:navlist.item icon="plus">Add Screen</flux:navlist.item>
            </flux:modal.trigger>
        </flux:navlist.group>
    </flux:navlist>
    <flux:navlist.group :heading="__('Collections')" expanded expandable>
        @foreach ($collections as $collection)
            <flux:navlist.item :href="route('collections', $collection->id)" :current="request()->routeIs('collections', $collection->id)">Collection: {{ $collection->name }}</flux:navlist.item>
        @endforeach
        <flux:modal.trigger name="add-collection">
            <flux:navlist.item icon="plus">Add Collection</flux:navlist.item>
        </flux:modal.trigger>
    </flux:navlist.group>
    <flux:modal name="add-screen">
        <form wire:submit.prevent="addScreen">
            <flux:input wire:model="name" label="Screen Name" description="Used to choose the screen." />
            <flux:button type="submit">Submit</flux:button>
        </form>
    </flux:modal>
    <flux:modal name="add-collection">
        <form wire:submit.prevent="addCollection">
            <flux:input wire:model="name" label="Collection Name" description="Used to choose the collection." />
            <flux:button type="submit">Submit</flux:button>
        </form>
    </flux:modal>
</div>
