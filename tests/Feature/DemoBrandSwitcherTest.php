<?php

use App\Support\DemoBrand;
use Livewire\Livewire;

test('por defecto usa Clarahipoteca', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Clarahipoteca', false)
        ->assertSee('Demo marca', false);
});

test('puede alternar a Crediservicios con el switch', function () {
    Livewire::test('demo-brand-switcher')
        ->set('brand', 'crediservicios')
        ->assertRedirect(route('home'));

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Crediservicios', false)
        ->assertSee('Precalifica tu crédito hipotecario', false)
        ->assertSee('data-theme="crediservicios"', false);
});

test('acepta demo por query string', function () {
    $this->get(route('home', ['demo' => 'crediservicios']))
        ->assertOk();

    expect(DemoBrand::currentKey())->toBe('crediservicios');
});
