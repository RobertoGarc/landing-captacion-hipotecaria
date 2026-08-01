<?php

use App\Enums\LeadStatus;
use App\Models\Lead;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Leads')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $leadId, string $status): void
    {
        $lead = Lead::query()->findOrFail($leadId);
        $lead->update(['status' => LeadStatus::from($status)]);

        Flux::toast(variant: 'success', text: 'Estado actualizado.');
    }

    #[Computed]
    public function leads(): LengthAwarePaginator
    {
        return Lead::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(15);
    }
}; ?>

<section class="w-full space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <flux:heading size="xl">Leads hipotecarios</flux:heading>
            <flux:text class="mt-1">Solicitudes recibidas desde la landing.</flux:text>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar nombre, email o teléfono" class="min-w-64" />
            <flux:select wire:model.live="status" class="min-w-40">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\LeadStatus::cases() as $case)
                    <option value="{{ $case->value }}">{{ $case->label() }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr class="text-left">
                    <th class="px-4 py-3 font-medium">Lead</th>
                    <th class="px-4 py-3 font-medium">Operación</th>
                    <th class="px-4 py-3 font-medium">UTM</th>
                    <th class="px-4 py-3 font-medium">Estado</th>
                    <th class="px-4 py-3 font-medium">Clientify</th>
                    <th class="px-4 py-3 font-medium">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->leads as $lead)
                    <tr wire:key="lead-{{ $lead->id }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.leads.show', $lead) }}" class="font-medium text-brand-700 hover:underline" wire:navigate>
                                {{ $lead->name }}
                            </a>
                            <div class="text-zinc-500">{{ $lead->email }} &middot; {{ $lead->phone }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $lead->purpose->label() }}</div>
                            <div class="text-zinc-500">{{ $lead->province }} &middot; {{ $lead->money($lead->financing_amount) }}</div>
                        </td>
                        <td class="px-4 py-3 text-zinc-500">
                            {{ collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign])->filter()->implode(' / ') ?: '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <select
                                class="rounded-lg border border-zinc-300 bg-transparent px-2 py-1 dark:border-zinc-600"
                                wire:change="updateStatus({{ $lead->id }}, $event.target.value)"
                            >
                                @foreach (\App\Enums\LeadStatus::cases() as $case)
                                    <option value="{{ $case->value }}" @selected($lead->status === $case)>{{ $case->label() }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if ($lead->isSyncedToClientify())
                                <span class="text-emerald-600">OK</span>
                            @elseif ($lead->clientify_error)
                                <span class="text-red-600" title="{{ $lead->clientify_error }}">Error</span>
                            @else
                                <span class="text-zinc-400">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $lead->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No hay leads todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->leads->links() }}
    </div>
</section>
