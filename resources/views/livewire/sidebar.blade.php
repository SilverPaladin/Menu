<?php

use Livewire\Component;

new class extends Component {
}; ?>

<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Manage')" expanded>
        <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
        <flux:navlist.item icon="tv" href="{{ route('dashboard') }}#screens">{{ __('Screens') }}</flux:navlist.item>
        <flux:navlist.item icon="rectangle-stack" href="{{ route('dashboard') }}#collections">{{ __('Collections') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
