<?php

use App\Support\DemoBrand;
use Livewire\Component;

new class extends Component
{
    public string $brand = '';

    public function mount(): void
    {
        $this->brand = DemoBrand::currentKey();

        $requested = collect(['demo', 'marca', 'brand'])
            ->map(fn (string $key) => request()->query($key))
            ->first(fn ($value) => is_string($value) && DemoBrand::exists($value));

        if (is_string($requested)) {
            DemoBrand::set($requested);
            $this->brand = $requested;
        }
    }

    public function updatedBrand(string $value): void
    {
        if (! DemoBrand::exists($value)) {
            return;
        }

        DemoBrand::set($value);

        $this->redirect(route('home'));
    }
}; ?>

<div
    class="fixed bottom-4 right-4 z-50 rounded-2xl border border-white/20 bg-brand-950/95 p-3 text-white shadow-2xl backdrop-blur sm:bottom-6 sm:right-6"
    wire:ignore.self
>
    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/55">Demo marca</p>
    <div class="flex items-center gap-3">
        <span class="text-xs font-medium {{ $brand === 'clarahipoteca' ? 'text-gold' : 'text-white/50' }}">Clara</span>
        <button
            type="button"
            role="switch"
            aria-checked="{{ $brand === 'crediservicios' ? 'true' : 'false' }}"
            aria-label="Alternar marca de demostración"
            wire:click="$set('brand', '{{ $brand === 'crediservicios' ? 'clarahipoteca' : 'crediservicios' }}')"
            class="relative h-7 w-12 rounded-full transition {{ $brand === 'crediservicios' ? 'bg-gold' : 'bg-brand-600' }}"
        >
            <span class="absolute top-0.5 left-0.5 size-6 rounded-full bg-white transition {{ $brand === 'crediservicios' ? 'translate-x-5' : 'translate-x-0' }}"></span>
        </button>
        <span class="text-xs font-medium {{ $brand === 'crediservicios' ? 'text-gold' : 'text-white/50' }}">Credi</span>
    </div>
    <p class="mt-2 max-w-[11rem] text-[11px] leading-snug text-white/45">
        {{ $brand === 'crediservicios' ? 'Crediservicios · Ecuador' : 'Clarahipoteca · demo' }}
    </p>
</div>
