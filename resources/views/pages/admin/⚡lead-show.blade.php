<?php

use App\Enums\LeadStatus;
use App\Jobs\SyncLeadToClientify;
use App\Models\Lead;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Detalle del lead')] class extends Component {
    #[Locked]
    public int $leadId;

    public string $admin_notes = '';

    public string $status;

    public function mount(Lead $lead): void
    {
        $this->leadId = $lead->id;
        $this->admin_notes = (string) $lead->admin_notes;
        $this->status = $lead->status->value;
    }

    public function lead(): Lead
    {
        return Lead::query()->findOrFail($this->leadId);
    }

    public function save(): void
    {
        $this->validate([
            'status' => ['required'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->lead()->update([
            'status' => LeadStatus::from($this->status),
            'admin_notes' => $this->admin_notes,
        ]);

        Flux::toast(variant: 'success', text: 'Lead actualizado.');
    }

    public function resyncClientify(): void
    {
        $lead = $this->lead();
        $lead->forceFill([
            'clientify_id' => null,
            'clientify_synced_at' => null,
            'clientify_error' => null,
        ])->save();

        SyncLeadToClientify::dispatch($lead);

        Flux::toast(variant: 'success', text: 'Sincronización con Clientify encolada.');
    }
}; ?>

@php($lead = $this->lead())

<section class="w-full space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ $lead->name }}</flux:heading>
            <flux:text class="mt-1">{{ $lead->email }} · {{ $lead->phone }}</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:button :href="route('admin.leads.index')" wire:navigate>Volver</flux:button>
            <flux:button variant="primary" wire:click="resyncClientify">Reenviar a Clientify</flux:button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">Operación</flux:heading>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-zinc-500">Objetivo</dt><dd>{{ $lead->purpose->label() }}</dd></div>
                <div><dt class="text-zinc-500">Provincia</dt><dd>{{ $lead->province }}</dd></div>
                <div><dt class="text-zinc-500">Precio</dt><dd>{{ $lead->money($lead->property_price) }}</dd></div>
                <div><dt class="text-zinc-500">Financiación</dt><dd>{{ $lead->money($lead->financing_amount) }}</dd></div>
                <div><dt class="text-zinc-500">Ahorros</dt><dd>{{ $lead->money($lead->savings_amount) }}</dd></div>
                <div><dt class="text-zinc-500">LTV</dt><dd>{{ $lead->loanToValueRatio() !== null ? $lead->loanToValueRatio().'%' : '—' }}</dd></div>
                <div><dt class="text-zinc-500">Titulares</dt><dd>{{ $lead->holders_count }}</dd></div>
                <div><dt class="text-zinc-500">Empleo</dt><dd>{{ $lead->employment_status->label() }}</dd></div>
                <div><dt class="text-zinc-500">Ingresos</dt><dd>{{ $lead->money($lead->monthly_income) }}</dd></div>
                <div><dt class="text-zinc-500">Deudas</dt><dd>{{ $lead->money($lead->monthly_debts) }}</dd></div>
            </dl>
        </div>

        <div class="space-y-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">Atribución y sync</flux:heading>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-zinc-500">UTM source</dt><dd>{{ $lead->utm_source ?: '—' }}</dd></div>
                <div><dt class="text-zinc-500">UTM medium</dt><dd>{{ $lead->utm_medium ?: '—' }}</dd></div>
                <div><dt class="text-zinc-500">UTM campaign</dt><dd>{{ $lead->utm_campaign ?: '—' }}</dd></div>
                <div><dt class="text-zinc-500">gclid / fbclid</dt><dd>{{ collect([$lead->gclid, $lead->fbclid])->filter()->implode(' / ') ?: '—' }}</dd></div>
                <div><dt class="text-zinc-500">Clientify ID</dt><dd>{{ $lead->clientify_id ?: '—' }}</dd></div>
                <div><dt class="text-zinc-500">Sync</dt><dd>{{ $lead->clientify_synced_at?->format('d/m/Y H:i') ?: 'Pendiente' }}</dd></div>
            </dl>
            @if ($lead->clientify_error)
                <p class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $lead->clientify_error }}</p>
            @endif
        </div>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
        <flux:heading size="lg">Gestión interna</flux:heading>
        <flux:select wire:model="status" label="Estado">
            @foreach (\App\Enums\LeadStatus::cases() as $case)
                <option value="{{ $case->value }}">{{ $case->label() }}</option>
            @endforeach
        </flux:select>
        <flux:textarea wire:model="admin_notes" label="Notas internas" rows="5" />
        <flux:button variant="primary" type="submit">Guardar</flux:button>
    </form>
</section>
