<?php

use App\Models\Collection;
use App\Models\Column;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('creating a screen from the dashboard redirects to its edit page', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('dashboard')
        ->set('name', 'Patio')
        ->call('addScreen')
        ->assertRedirect(route('screens', Screen::sole()));

    expect(Screen::sole()->name)->toBe('Patio');
});

test('creating a collection from the dashboard redirects to its edit page', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('dashboard')
        ->set('name', 'Beast Mode')
        ->call('addCollection')
        ->assertRedirect(route('collections', Collection::sole()));

    expect(Collection::sole()->name)->toBe('Beast Mode');
});

test('deleting a screen removes it and cascades its columns', function () {
    $user = User::factory()->create();
    $screen = Screen::create(['name' => 'Main']);
    Column::create(['screen_id' => $screen->id, 'name' => 'Left']);

    Livewire::actingAs($user)
        ->test('dashboard')
        ->call('deleteScreen', $screen->id);

    expect(Screen::find($screen->id))->toBeNull()
        ->and(Column::where('screen_id', $screen->id)->count())->toBe(0);
});

test('deleting a collection removes it and its stored image', function () {
    Storage::fake('public');
    Storage::disk('public')->put('collection-images/header.png', 'img');

    $user = User::factory()->create();
    $collection = Collection::create([
        'name' => 'Beast Mode',
        'image' => 'collection-images/header.png',
    ]);

    Livewire::actingAs($user)
        ->test('dashboard')
        ->call('deleteCollection', $collection->id);

    expect(Collection::find($collection->id))->toBeNull();
    Storage::disk('public')->assertMissing('collection-images/header.png');
});

test('screen and collection names are required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('dashboard')
        ->set('name', '')
        ->call('addScreen')
        ->assertHasErrors('name')
        ->call('addCollection')
        ->assertHasErrors('name');
});
