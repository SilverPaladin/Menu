<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('editor','editor')->name('edit');
Volt::route('viewer','viewer')->name('view');
