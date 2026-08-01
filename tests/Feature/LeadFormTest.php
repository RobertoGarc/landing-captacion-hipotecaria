<?php

use App\Enums\EmploymentStatus;
use App\Enums\MortgagePurpose;
use App\Jobs\SyncLeadToClientify;
use App\Mail\LeadConfirmationMail;
use App\Mail\NewLeadNotificationMail;
use App\Models\Lead;
use App\Support\DemoBrand;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    config(['services.leads.notify_emails' => 'equipo@example.com']);
    $this->seed(\Database\Seeders\SiteSettingSeeder::class);
});

test('la landing carga correctamente', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Clarahipoteca', false);
});

test('el formulario multipaso valida el primer paso', function () {
    Livewire::test('lead-form')
        ->call('nextStep')
        ->assertHasErrors(['purpose']);
});

test('se puede completar el estudio y crear un lead', function () {
    Mail::fake();
    Queue::fake();

    Livewire::test('lead-form')
        ->set('purpose', MortgagePurpose::Purchase->value)
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('property_price', 250000)
        ->set('province', 'Madrid')
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('financing_amount', 200000)
        ->set('savings_amount', 50000)
        ->call('nextStep')
        ->assertSet('step', 4)
        ->set('holders_count', 2)
        ->set('employment_status', EmploymentStatus::Permanent->value)
        ->call('nextStep')
        ->assertSet('step', 5)
        ->set('monthly_income', 3200)
        ->set('monthly_debts', 250)
        ->call('nextStep')
        ->assertSet('step', 6)
        ->set('name', 'Ana Pérez')
        ->set('email', 'ana@example.com')
        ->set('phone', '612345678')
        ->set('privacy_accepted', true)
        ->set('utm_source', 'google')
        ->set('utm_campaign', 'hipotecas-q1')
        ->call('submit')
        ->assertSet('submitted', true)
        ->assertSee('Solicitud enviada');

    $lead = Lead::query()->first();

    expect($lead)->not->toBeNull()
        ->and($lead->email)->toBe('ana@example.com')
        ->and($lead->utm_source)->toBe('google')
        ->and($lead->utm_campaign)->toBe('hipotecas-q1')
        ->and($lead->purpose)->toBe(MortgagePurpose::Purchase);

    Mail::assertQueued(LeadConfirmationMail::class);
    Mail::assertQueued(NewLeadNotificationMail::class);
    Queue::assertPushed(SyncLeadToClientify::class);
});

test('con la marca de Ecuador el formulario usa dólares y guarda USD', function () {
    Mail::fake();
    Queue::fake();

    DemoBrand::set('crediservicios');

    Livewire::test('lead-form')
        ->set('purpose', MortgagePurpose::Purchase->value)
        ->call('nextStep')
        ->assertSee('Precio aproximado ($)')
        ->assertDontSee('Precio aproximado (€)')
        ->set('property_price', 95000)
        ->set('province', 'Pichincha')
        ->call('nextStep')
        ->set('financing_amount', 70000)
        ->set('savings_amount', 25000)
        ->call('nextStep')
        ->set('holders_count', 1)
        ->set('employment_status', EmploymentStatus::Permanent->value)
        ->call('nextStep')
        ->assertSee('Ingresos netos mensuales ($)')
        ->set('monthly_income', 1800)
        ->set('monthly_debts', 200)
        ->call('nextStep')
        ->set('name', 'Andrea Pilco')
        ->set('email', 'andrea@example.com')
        ->set('phone', '0991234567')
        ->set('privacy_accepted', true)
        ->call('submit')
        ->assertSet('submitted', true);

    expect(Lead::query()->first()->currency)->toBe('USD');
});

test('el honeypot descarta envíos spam sin crear lead', function () {
    Mail::fake();
    Queue::fake();

    Livewire::test('lead-form')
        ->set('purpose', MortgagePurpose::Purchase->value)
        ->set('property_price', 250000)
        ->set('province', 'Madrid')
        ->set('financing_amount', 200000)
        ->set('savings_amount', 50000)
        ->set('holders_count', 1)
        ->set('employment_status', EmploymentStatus::Permanent->value)
        ->set('monthly_income', 3200)
        ->set('monthly_debts', 0)
        ->set('name', 'Bot')
        ->set('email', 'bot@example.com')
        ->set('phone', '612345678')
        ->set('privacy_accepted', true)
        ->set('website', 'http://spam.test')
        ->set('step', 6)
        ->call('submit')
        ->assertSet('submitted', true);

    expect(Lead::query()->count())->toBe(0);
    Mail::assertNothingQueued();
    Queue::assertNothingPushed();
});
