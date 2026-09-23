<?php

use App\Models\Collection;
use App\Models\Screen;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component {
    #[Validate('required|string|max:255')]
    public $name = '';

    public function with()
    {
        return [
            'screens' => Screen::orderBy('name')->with('columns.collection.items')->get(),
            'collections' => Collection::orderBy('name')->with(['items', 'columns.screen'])->get(),
        ];
    }

    public function addScreen()
    {
        $this->validate();
        $screen = Screen::create(['name' => $this->name]);
        $this->redirectRoute('screens', $screen);
    }

    public function addCollection()
    {
        $this->validate();
        $collection = Collection::create(['name' => $this->name]);
        $this->redirectRoute('collections', $collection);
    }

    public function deleteScreen($id)
    {
        Screen::findOrFail($id)->delete();
        session()->flash('message', 'Screen deleted.');
    }

    public function deleteCollection($id)
    {
        $collection = Collection::findOrFail($id);
        if ($collection->image) {
            Storage::disk('public')->delete($collection->image);
        }
        $collection->delete();
        session()->flash('message', 'Collection deleted.');
    }
};
?>

<div class="p-6 space-y-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>Manage your screens and collections, then launch them on the TV.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="tv" href="{{ route('viewer') }}" target="_blank">Launch Viewer</flux:button>
    </div>

    @if (session('message'))
        <flux:callout variant="success" icon="check-circle" :text="session('message')" />
    @endif

    @if ($screens->isEmpty() && $collections->isEmpty())
        <flux:card>
            <flux:heading size="lg">Welcome — let's set up your first display</flux:heading>
            <div class="mt-4 space-y-4">
                <div class="flex gap-3">
                    <flux:badge color="zinc">1</flux:badge>
                    <flux:text><strong class="text-zinc-900 dark:text-white">Create a collection</strong> — a group of items shown in one column (name, header image, items).</flux:text>
                </div>
                <div class="flex gap-3">
                    <flux:badge color="zinc">2</flux:badge>
                    <flux:text><strong class="text-zinc-900 dark:text-white">Create a screen</strong> — add columns and assign a collection to each one.</flux:text>
                </div>
                <div class="flex gap-3">
                    <flux:badge color="zinc">3</flux:badge>
                    <flux:text><strong class="text-zinc-900 dark:text-white">Launch the viewer</strong> — pick a screen to show it on the TV.</flux:text>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <flux:modal.trigger name="add-collection">
                    <flux:button variant="primary" icon="plus">Create Collection</flux:button>
                </flux:modal.trigger>
                <flux:modal.trigger name="add-screen">
                    <flux:button icon="plus">Create Screen</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>
    @endif

    <section id="screens" class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">Screens</flux:heading>
            <flux:modal.trigger name="add-screen">
                <flux:button variant="primary" size="sm" icon="plus">New Screen</flux:button>
            </flux:modal.trigger>
        </div>

        @if ($screens->isEmpty())
            <flux:card>
                <flux:text class="text-center">No screens yet. A screen arranges collections into columns for the TV.</flux:text>
            </flux:card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($screens as $screen)
                    @php
                        $itemCount = $screen->columns->sum(fn ($c) => $c->collection?->items->count() ?? 0);
                        $hiddenCount = $screen->columns->sum(fn ($c) => $c->collection?->items->where('active', false)->count() ?? 0);
                    @endphp
                    <flux:card size="sm" class="space-y-3">
                        <div class="pointer-events-none">
                            <x-screen-preview :$screen />
                        </div>
                        <div>
                            <flux:heading>{{ $screen->name }}</flux:heading>
                            <flux:text size="sm">
                                {{ $screen->columns->count() }} {{ Str::plural('column', $screen->columns->count()) }}
                                · {{ $itemCount }} {{ Str::plural('item', $itemCount) }}
                                @if ($hiddenCount) · {{ $hiddenCount }} hidden @endif
                            </flux:text>
                        </div>
                        <div class="flex gap-2">
                            <flux:button size="sm" icon="pencil-square" href="{{ route('screens', $screen) }}">Edit</flux:button>
                            <flux:button size="sm" variant="ghost" icon="tv" href="{{ route('viewer', ['screen' => $screen->id]) }}" target="_blank">View on TV</flux:button>
                            <flux:spacer />
                            <flux:button size="sm" variant="ghost" icon="trash"
                                wire:confirm="Delete screen &quot;{{ $screen->name }}&quot; and all its columns?"
                                wire:click="deleteScreen({{ $screen->id }})" />
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </section>

    <section id="collections" class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">Collections</flux:heading>
            <flux:modal.trigger name="add-collection">
                <flux:button variant="primary" size="sm" icon="plus">New Collection</flux:button>
            </flux:modal.trigger>
        </div>

        @if ($collections->isEmpty())
            <flux:card>
                <flux:text class="text-center">No collections yet. A collection is a group of items displayed in a column.</flux:text>
            </flux:card>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($collections as $collection)
                    @php
                        $hiddenCount = $collection->items->where('active', false)->count();
                        $usedOn = $collection->columns->pluck('screen.name')->filter()->unique()->values();
                    @endphp
                    <flux:card size="sm" class="space-y-3">
                        <div class="flex items-center gap-3">
                            @if ($collection->image)
                                <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}"
                                    class="h-10 w-16 object-contain rounded bg-zinc-100 dark:bg-zinc-800 shrink-0">
                            @else
                                <div class="h-10 w-16 rounded bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                    <flux:icon.photo class="size-5 text-zinc-400" />
                                </div>
                            @endif
                            <div class="min-w-0">
                                <flux:heading class="truncate">{{ $collection->name }}</flux:heading>
                                <flux:text size="sm" class="truncate">
                                    {!! collect([$collection->puff_count, $collection->volume])->filter()->implode(' · ') ?: '—' !!}
                                </flux:text>
                            </div>
                        </div>
                        <flux:text size="sm">
                            {{ $collection->items->count() }} {{ Str::plural('item', $collection->items->count()) }}
                            @if ($hiddenCount) · {{ $hiddenCount }} hidden @endif
                            @if ($collection->font_size) · {{ $collection->font_size }}px @endif
                        </flux:text>
                        <div class="flex flex-wrap gap-1 min-h-6">
                            @forelse ($usedOn as $screenName)
                                <flux:badge size="sm" color="zinc" icon="tv">{{ $screenName }}</flux:badge>
                            @empty
                                <flux:badge size="sm">Not on any screen</flux:badge>
                            @endforelse
                        </div>
                        <div class="flex gap-2">
                            <flux:button size="sm" icon="pencil-square" href="{{ route('collections', $collection) }}">Edit</flux:button>
                            <flux:spacer />
                            <flux:button size="sm" variant="ghost" icon="trash"
                                wire:confirm="Delete collection &quot;{{ $collection->name }}&quot;{{ $usedOn->isNotEmpty() ? ' — it is currently used on: ' . $usedOn->implode(', ') : '' }}?"
                                wire:click="deleteCollection({{ $collection->id }})" />
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </section>

    <flux:accordion>
        <flux:accordion.item heading="How this system works">
            <div class="space-y-4 text-sm">
                <div>
                    <flux:heading size="sm">1. Collections hold your items</flux:heading>
                    <flux:text class="mt-1">Each collection has a header image, a puff count and volume line, and a list of items. Items can be hidden without deleting them — useful for out-of-stock products.</flux:text>
                </div>
                <div>
                    <flux:heading size="sm">2. Screens arrange collections into columns</flux:heading>
                    <flux:text class="mt-1">A screen is a TV layout: add columns, then assign one collection to each column. The same collection can appear on multiple screens.</flux:text>
                </div>
                <div>
                    <flux:heading size="sm">3. The viewer displays a screen on the TV</flux:heading>
                    <flux:text class="mt-1">Launch the viewer and pick a screen. On the TV you can click an item to hide it, or hover near the bottom of a column for quick edit controls.</flux:text>
                </div>
                <flux:text class="font-medium">Tips: use high-contrast header images, keep item names concise, and increase font size for distant viewing.</flux:text>
            </div>
        </flux:accordion.item>
    </flux:accordion>

    <flux:modal name="add-screen" class="md:w-96">
        <form wire:submit="addScreen" class="space-y-6">
            <flux:heading size="lg">New Screen</flux:heading>
            <flux:input wire:model="name" label="Screen Name" description="Used to identify this screen in the viewer." autofocus />
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create & Configure</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="add-collection" class="md:w-96">
        <form wire:submit="addCollection" class="space-y-6">
            <flux:heading size="lg">New Collection</flux:heading>
            <flux:input wire:model="name" label="Collection Name" description="e.g. Beast Mode Max." autofocus />
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create & Configure</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
