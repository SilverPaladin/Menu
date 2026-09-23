<?php

use App\Models\Collection;
use App\Models\Column;
use App\Models\Item;
use App\Models\Screen;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('dashboard lists screens, collections, and preview items', function () {
    $user = User::factory()->create();

    $collection = Collection::create(['name' => 'Beast Mode', 'puff_count' => '25,000 Puffs']);
    $screen = Screen::create(['name' => 'Main Board']);
    Column::create(['screen_id' => $screen->id, 'collection_id' => $collection->id, 'name' => 'Left']);
    Item::create(['collection_id' => $collection->id, 'name' => 'Blue Razz', 'active' => true]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Main Board')
        ->assertSee('Beast Mode')
        ->assertSee('Blue Razz');
});

test('guests cannot access screen or collection edit pages', function () {
    $screen = Screen::create(['name' => 'Main']);
    $collection = Collection::create(['name' => 'Beast Mode']);

    $this->get("/screens/{$screen->id}")->assertRedirect('/login');
    $this->get("/collections/{$collection->id}")->assertRedirect('/login');
});

test('authenticated users can access edit pages', function () {
    $user = User::factory()->create();
    $screen = Screen::create(['name' => 'Main']);
    $collection = Collection::create(['name' => 'Beast Mode']);

    $this->actingAs($user)
        ->get("/screens/{$screen->id}")->assertOk();
    $this->actingAs($user)
        ->get("/collections/{$collection->id}")->assertOk();
});
