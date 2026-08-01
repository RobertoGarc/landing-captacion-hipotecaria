<?php

use App\Models\SiteSetting;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Contenido de la landing')] class extends Component {
    /** @var array<int, array{id:int,key:string,label:string,type:string,help:?string,value:mixed}> */
    public array $settings = [];

    public function mount(): void
    {
        $this->settings = SiteSetting::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (SiteSetting $setting) => [
                'id' => $setting->id,
                'key' => $setting->key,
                'label' => $setting->label,
                'type' => $setting->type,
                'help' => $setting->help,
                'value' => is_array($setting->value) || is_object($setting->value)
                    ? json_encode($setting->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    : (string) $setting->value,
            ])
            ->all();
    }

    public function save(): void
    {
        foreach ($this->settings as $settingData) {
            $setting = SiteSetting::query()->findOrFail($settingData['id']);
            $value = $settingData['value'];

            if (in_array($setting->type, ['json'], true)) {
                $decoded = json_decode((string) $value, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->addError('settings.'.$setting->id, 'JSON inválido en '.$setting->label);

                    return;
                }

                $value = $decoded;
            }

            $setting->update(['value' => $value]);
        }

        SiteSetting::flushCache();

        Flux::toast(variant: 'success', text: 'Contenido actualizado.');
    }
}; ?>

<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl">Editar contenidos de la landing</flux:heading>
        <flux:text class="mt-1">Textos, imagen hero, ventajas, proceso, testimonios y FAQ.</flux:text>
    </div>

    <form wire:submit="save" class="space-y-5">
        @foreach ($settings as $index => $setting)
            <div wire:key="setting-{{ $setting['id'] }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:heading size="sm">{{ $setting['label'] }}</flux:heading>
                <flux:text class="mb-3 text-xs">Clave: {{ $setting['key'] }}</flux:text>

                @if ($setting['type'] === 'textarea' || $setting['type'] === 'json')
                    <flux:textarea wire:model="settings.{{ $index }}.value" rows="{{ $setting['type'] === 'json' ? 10 : 4 }}" />
                @else
                    <flux:input wire:model="settings.{{ $index }}.value" />
                @endif

                @if ($setting['help'])
                    <flux:text class="mt-2 text-xs">{{ $setting['help'] }}</flux:text>
                @endif

                @error('settings.'.$setting['id'])
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <flux:button variant="primary" type="submit">Guardar contenidos</flux:button>
    </form>
</section>
