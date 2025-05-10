<?php

use Livewire\Volt\Component;
use App\Models\Screen;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

new class extends Component {
    public $screens;
    #[Validate('required|string|max:255')]
    public $name;
    #[Url]
    public $screen = null;
    public $errorMessage = null;

    public function mount()
    {
        $this->screens = Screen::orderBy('name')->get();
        if($this->screen && !Screen::find($this->screen)){
            $this->errorMessage = "Screen with ID {$this->screen} was not found.";
            $this->resetScreen();
        }

    }

    #[On('screen-reset')]
    public function resetScreen()
    {
        $this->screen = null;
    }

    public function createScreen()
    {
        $this->validate();
        $screen = Screen::create([
            'name' => $this->name,
        ]);

        $this->reset(['name']);
        $this->screens = Screen::orderBy('name')->get();
        $this->screen = $screen->id;
    }

    public function selectScreen($id)
    {
        $this->screen = $id;
    }
}; ?>

<div
    class="h-dvh flex items-center justify-center w-full text-gray-100 bg-black">
    @if (!$screen)
        <!-- Screen Selection Interface -->
        <main class="flex w-full gap-8 p-4 justify-center flex-col">
            @if ($errorMessage)
                <div class="bg-red-500 text-white p-4 rounded-lg mb-4 text-center">
                    {{ $errorMessage }}
                </div>
            @endif
            <div class="flex w-full gap-8 justify-center">


            @foreach ($screens as $scr)
                <div wire:click="selectScreen({{ $scr->id }})"
                    class="max-w-1/4 bg-zinc-900 text-center items-center flex flex-col justify-center flex-1 p-4 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                    <h1 class="font-bold text-2xl">{{ $scr->name }}</h1>
                </div>
            @endforeach

            <!-- Add Screen Panel -->
            <div class="margin-auto max-w-1/4 flex-1 text-lg p-6 bg-zinc-900 rounded-lg flex flex-col gap-4">
                <h2 class="text-2xl font-bold mb-6 text-center">Add Screen</h2>
                <flux:input wire:model="name" label="Screen Name" description="Used to choose the screen." />
                <flux:button wire:click="createScreen" wire:loading.attr="disabled">Submit</flux:button>
            </div>
            </div>
        </main>
    @else
        <livewire:screen :$screen />
    @endif
</div>
