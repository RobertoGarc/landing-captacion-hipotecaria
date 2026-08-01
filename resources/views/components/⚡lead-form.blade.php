<?php

use App\Enums\EmploymentStatus;
use App\Enums\MortgagePurpose;
use App\Services\LeadIntakeService;
use App\Support\DemoBrand;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    public int $step = 1;

    #[Locked]
    public int $totalSteps = 6;

    public string $purpose = '';

    public ?int $property_price = null;

    public string $province = '';

    public ?int $financing_amount = null;

    public ?int $savings_amount = null;

    public int $holders_count = 1;

    public string $employment_status = '';

    public ?int $monthly_income = null;

    public ?int $monthly_debts = 0;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public bool $privacy_accepted = false;

    public bool $marketing_accepted = false;

    public string $website = '';

    public ?string $utm_source = null;

    public ?string $utm_medium = null;

    public ?string $utm_campaign = null;

    public ?string $utm_term = null;

    public ?string $utm_content = null;

    public ?string $gclid = null;

    public ?string $fbclid = null;

    public ?string $referrer = null;

    public ?string $landing_url = null;

    public bool $submitted = false;

    public function mount(): void
    {
        $request = request();

        $this->utm_source = $request->query('utm_source', session('utm_source'));
        $this->utm_medium = $request->query('utm_medium', session('utm_medium'));
        $this->utm_campaign = $request->query('utm_campaign', session('utm_campaign'));
        $this->utm_term = $request->query('utm_term', session('utm_term'));
        $this->utm_content = $request->query('utm_content', session('utm_content'));
        $this->gclid = $request->query('gclid', session('gclid'));
        $this->fbclid = $request->query('fbclid', session('fbclid'));
        $this->referrer = $request->headers->get('referer');
        $this->landing_url = $request->fullUrl();

        session([
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term' => $this->utm_term,
            'utm_content' => $this->utm_content,
            'gclid' => $this->gclid,
            'fbclid' => $this->fbclid,
        ]);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['website'], true)) {
            return;
        }

        $this->validateOnly($property, $this->rulesForStep($this->step));
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > $this->step) {
            return;
        }

        $this->step = $step;
    }

    public function submit(LeadIntakeService $intake): void
    {
        if (filled($this->website)) {
            $this->submitted = true;

            return;
        }

        $key = 'lead-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Has enviado demasiadas solicitudes. Inténtalo de nuevo más tarde.');

            return;
        }

        RateLimiter::hit($key, 3600);

        $validated = $this->validate($this->rulesForStep($this->totalSteps, includeAll: true));

        $intake->store([
            ...$validated,
            'currency' => DemoBrand::currency(),
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term' => $this->utm_term,
            'utm_content' => $this->utm_content,
            'gclid' => $this->gclid,
            'fbclid' => $this->fbclid,
            'referrer' => $this->referrer,
            'landing_url' => $this->landing_url,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->submitted = true;

        $currency = DemoBrand::currency();

        $this->js(<<<JS
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'lead_submitted', lead_type: 'mortgage_study' });
            if (typeof gtag === 'function') {
                gtag('event', 'generate_lead', { currency: '{$currency}', value: 1 });
            }
            if (typeof fbq === 'function') {
                fbq('track', 'Lead');
            }
        JS);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesForStep(int $step, bool $includeAll = false): array
    {
        $rules = [
            1 => [
                'purpose' => ['required', Rule::enum(MortgagePurpose::class)],
            ],
            2 => [
                'property_price' => ['required', 'integer', 'min:30000', 'max:5000000'],
                'province' => ['required', 'string', Rule::in(array_keys(DemoBrand::provinces()))],
            ],
            3 => [
                'financing_amount' => ['required', 'integer', 'min:10000', 'max:5000000'],
                'savings_amount' => ['required', 'integer', 'min:0', 'max:5000000'],
            ],
            4 => [
                'holders_count' => ['required', 'integer', 'min:1', 'max:4'],
                'employment_status' => ['required', Rule::enum(EmploymentStatus::class)],
            ],
            5 => [
                'monthly_income' => ['required', 'integer', 'min:500', 'max:100000'],
                'monthly_debts' => ['required', 'integer', 'min:0', 'max:100000'],
            ],
            6 => [
                'name' => ['required', 'string', 'min:2', 'max:120'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'regex:'.$this->phoneRegex()],
                'privacy_accepted' => ['accepted'],
                'marketing_accepted' => ['boolean'],
            ],
        ];

        if ($includeAll) {
            return collect($rules)->flatMap(fn (array $stepRules) => $stepRules)->all();
        }

        return $rules[$step] ?? [];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'purpose.required' => 'Selecciona el objetivo de tu hipoteca.',
            'property_price.required' => 'Indica el precio aproximado del inmueble.',
            'province.required' => 'Selecciona la provincia.',
            'financing_amount.required' => 'Indica la financiación que necesitas.',
            'savings_amount.required' => 'Indica tus ahorros disponibles.',
            'holders_count.required' => 'Indica el número de titulares.',
            'employment_status.required' => 'Selecciona tu situación laboral.',
            'monthly_income.required' => 'Indica tus ingresos mensuales netos.',
            'monthly_debts.required' => 'Indica tus deudas mensuales (0 si no tienes).',
            'name.required' => 'Escribe tu nombre completo.',
            'email.required' => 'Necesitamos un email válido para contactarte.',
            'phone.required' => 'Introduce un móvil español de 9 dígitos.',
            'phone.regex' => DemoBrand::country() === 'EC'
                ? 'Introduce un móvil ecuatoriano válido (10 dígitos, empieza por 09).'
                : 'Introduce un móvil español válido (9 dígitos, empieza por 6-9).',
            'privacy_accepted.accepted' => 'Debes aceptar la política de privacidad.',
        ];
    }

    protected function phoneRegex(): string
    {
        return DemoBrand::country() === 'EC'
            ? '/^09\d{8}$/'
            : '/^[6-9]\d{8}$/';
    }

    public function currencySymbol(): string
    {
        return DemoBrand::currencySymbol();
    }

    public function progressPercent(): int
    {
        return (int) round(($this->step / $this->totalSteps) * 100);
    }

    /**
     * @return list<string>
     */
    public function stepLabels(): array
    {
        return [
            1 => 'Objetivo',
            2 => 'Inmueble',
            3 => 'Financiación',
            4 => 'Titulares',
            5 => 'Economía',
            6 => 'Contacto',
        ];
    }

    public function needsSavingsEmphasis(): bool
    {
        return $this->purpose === MortgagePurpose::Purchase->value;
    }
}; ?>

<section id="estudio" class="scroll-mt-24" wire:cloak>
    @if ($submitted)
        <div class="rounded-2xl border border-brand-200 bg-brand-50 p-8 text-center shadow-sm" data-testid="lead-success">
            <p class="font-display text-2xl text-brand-950">Solicitud enviada</p>
            <p class="mt-3 text-brand-800/80">
                Gracias. Hemos recibido tu estudio inicial y te contactaremos en breve.
            </p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_20px_60px_-30px_rgba(15,61,46,0.45)]">
            <div class="border-b border-stone-100 bg-stone-50 px-5 py-4 sm:px-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700">Estudio inicial</p>
                        <p class="mt-1 text-sm text-stone-600">Paso {{ $step }} de {{ $totalSteps }} · {{ $this->stepLabels()[$step] }}</p>
                    </div>
                    <p class="text-sm font-semibold text-brand-800">{{ $this->progressPercent() }}%</p>
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-stone-200" role="progressbar" aria-valuenow="{{ $this->progressPercent() }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-brand-700 transition-all duration-500 ease-out" style="width: {{ $this->progressPercent() }}%"></div>
                </div>
                <ol class="mt-4 hidden grid-cols-6 gap-2 sm:grid">
                    @foreach ($this->stepLabels() as $index => $label)
                        <li>
                            <button
                                type="button"
                                wire:click="goToStep({{ $index }})"
                                @disabled($index > $step)
                                class="w-full truncate text-left text-[11px] font-medium {{ $index === $step ? 'text-brand-800' : ($index < $step ? 'text-brand-600' : 'text-stone-400') }}"
                            >
                                {{ $label }}
                            </button>
                        </li>
                    @endforeach
                </ol>
            </div>

            <form
                wire:submit="{{ $step === $totalSteps ? 'submit' : 'nextStep' }}"
                class="space-y-6 px-5 py-6 sm:px-8 sm:py-8"
                novalidate
            >
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="website">Website</label>
                    <input id="website" type="text" wire:model="website" tabindex="-1" autocomplete="off" />
                </div>

                @if ($step === 1)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">¿Qué necesitas?</h3>
                        <p class="mt-1 text-sm text-stone-600">Elige el objetivo principal de tu solicitud.</p>
                        <div class="mt-5 grid gap-3">
                            @foreach (\App\Enums\MortgagePurpose::cases() as $option)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-3 transition {{ $purpose === $option->value ? 'border-brand-700 bg-brand-50' : 'border-stone-200 hover:border-brand-300' }}">
                                    <input type="radio" class="mt-1 accent-brand-700" wire:model.live="purpose" value="{{ $option->value }}" />
                                    <span class="text-sm font-medium text-brand-950">{{ $option->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('purpose') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if ($step === 2)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">Datos del inmueble</h3>
                        <p class="mt-1 text-sm text-stone-600">Con esto estimamos el escenario de financiación.</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="property_price">Precio aproximado ({{ $this->currencySymbol() }})</label>
                                <input id="property_price" type="number" wire:model.blur="property_price" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" min="30000" step="1000" />
                                @error('property_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="province">Provincia</label>
                                <select id="province" wire:model.blur="province" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2">
                                    <option value="">Selecciona provincia</option>
                                    @foreach (\App\Support\DemoBrand::provinces() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('province') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step === 3)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">Financiación y ahorros</h3>
                        <p class="mt-1 text-sm text-stone-600">
                            @if ($this->needsSavingsEmphasis())
                                En compra, los ahorros marcan la entrada y los gastos asociados.
                            @else
                                Indica cuánto quieres financiar y qué liquidez tienes disponible.
                            @endif
                        </p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="financing_amount">Financiación deseada ({{ $this->currencySymbol() }})</label>
                                <input id="financing_amount" type="number" wire:model.blur="financing_amount" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" min="10000" step="1000" />
                                @error('financing_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="savings_amount">Ahorros disponibles ({{ $this->currencySymbol() }})</label>
                                <input id="savings_amount" type="number" wire:model.blur="savings_amount" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" min="0" step="1000" />
                                @error('savings_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step === 4)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">Titulares y empleo</h3>
                        <p class="mt-1 text-sm text-stone-600">La situación laboral influye en la viabilidad de la operación.</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="holders_count">Nº de titulares</label>
                                <select id="holders_count" wire:model.blur="holders_count" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('holders_count') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="employment_status">Situación laboral principal</label>
                                <select id="employment_status" wire:model.blur="employment_status" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2">
                                    <option value="">Selecciona</option>
                                    @foreach (\App\Enums\EmploymentStatus::cases() as $option)
                                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                                    @endforeach
                                </select>
                                @error('employment_status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step === 5)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">Ingresos y deudas</h3>
                        <p class="mt-1 text-sm text-stone-600">Usa cifras netas mensuales aproximadas del hogar solicitante.</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="monthly_income">Ingresos netos mensuales ({{ $this->currencySymbol() }})</label>
                                <input id="monthly_income" type="number" wire:model.blur="monthly_income" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" min="500" step="50" />
                                @error('monthly_income') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="monthly_debts">Deudas mensuales ({{ $this->currencySymbol() }})</label>
                                <input id="monthly_debts" type="number" wire:model.blur="monthly_debts" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" min="0" step="50" />
                                @error('monthly_debts') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step === 6)
                    <div>
                        <h3 class="font-display text-xl text-brand-950">Tus datos de contacto</h3>
                        <p class="mt-1 text-sm text-stone-600">Te contactaremos para completar el estudio y resolver dudas.</p>
                        <div class="mt-5 grid gap-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700" for="name">Nombre completo</label>
                                <input id="name" type="text" wire:model.blur="name" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" autocomplete="name" />
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700" for="email">Email</label>
                                    <input id="email" type="email" wire:model.blur="email" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" autocomplete="email" />
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700" for="phone">Móvil</label>
                                    <input id="phone" type="tel" wire:model.blur="phone" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none ring-brand-700 focus:ring-2" autocomplete="tel" placeholder="{{ \App\Support\DemoBrand::country() === 'EC' ? '0991234567' : '612345678' }}" />
                                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <label class="flex items-start gap-3 text-sm text-stone-700">
                                <input type="checkbox" class="mt-1 accent-brand-700" wire:model="privacy_accepted" />
                                <span>Acepto la <a href="{{ route('privacy') }}" class="underline decoration-brand-700/40 underline-offset-2" target="_blank" rel="noopener">política de privacidad</a> y el tratamiento de mis datos para gestionar esta solicitud.</span>
                            </label>
                            @error('privacy_accepted') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            <label class="flex items-start gap-3 text-sm text-stone-700">
                                <input type="checkbox" class="mt-1 accent-brand-700" wire:model="marketing_accepted" />
                                <span>Quiero recibir información comercial sobre productos y mejoras hipotecarias (opcional).</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col-reverse gap-3 border-t border-stone-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="button"
                        wire:click="previousStep"
                        @disabled($step === 1)
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Atrás
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center rounded-xl bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-900 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="nextStep,submit">
                            {{ $step === $totalSteps ? 'Enviar estudio' : 'Continuar' }}
                        </span>
                        <span wire:loading wire:target="nextStep,submit">Procesando…</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</section>
