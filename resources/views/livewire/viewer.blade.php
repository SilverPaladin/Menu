<?php

use Livewire\Volt\Component;
use App\Models\Screen;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app.viewer')] class extends Component {
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
    class="h-dvh flex items-center justify-center w-full text-gray-100 bg-black"
    x-data="{ 
        isFullScreen: false,
        toggleFullScreen() { 
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    alert(`Error attempting to enable full-screen mode: ${err.message}`);
                });
                this.isFullScreen = true;
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    this.isFullScreen = false;
                }
            }
        }
    }">
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
                <a class="text-center text-white font-bold border p-2 rounded-lg" href="{{ route('dashboard')}}">Visit Dashboard</a>
                <h2 class="text-2xl font-bold mb-6 text-center">Add Screen</h2>
                <flux:input wire:model="name" label="Screen Name" description="Used to choose the screen." />
                <flux:button wire:click="createScreen">Submit</flux:button>
                <flux:button @click="toggleFullScreen()" x-text="isFullScreen ? 'Exit Full Screen' : 'Go Full Screen'">Go Full Screen</flux:button>
            </div>
        </main>
    @else
        <livewire:screen :$screen />
    @endif
</div>
