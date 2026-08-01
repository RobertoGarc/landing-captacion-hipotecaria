<?php

use App\Models\Lead;
use App\Models\User;

test('los invitados no pueden ver el panel de leads', function () {
    $this->get(route('admin.leads.index'))
        ->assertRedirect(route('login'));
});

test('un usuario autenticado puede ver leads', function () {
    $user = User::factory()->create();
    $lead = Lead::factory()->create([
        'name' => 'María López',
        'email' => 'maria@example.com',
    ]);

    $this->actingAs($user)
        ->get(route('admin.leads.index'))
        ->assertOk()
        ->assertSee('María López')
        ->assertSee('maria@example.com');

    $this->actingAs($user)
        ->get(route('admin.leads.show', $lead))
        ->assertOk()
        ->assertSee('María López');
});

test('los leads de Ecuador se muestran en dólares', function () {
    $user = User::factory()->create();
    $lead = Lead::factory()->ecuador()->create([
        'property_price' => 95000,
        'financing_amount' => 70000,
    ]);

    $this->actingAs($user)
        ->get(route('admin.leads.show', $lead))
        ->assertOk()
        ->assertSee('$95,000')
        ->assertDontSee('95.000 €');
});

test('el dashboard redirige al listado de leads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.leads.index'));
});

test('un usuario autenticado puede editar contenidos', function () {
    $this->seed(\Database\Seeders\SiteSettingSeeder::class);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.content.edit'))
        ->assertOk()
        ->assertSee('Editar contenidos');
});
