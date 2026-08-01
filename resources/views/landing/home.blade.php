@php
    use App\Support\DemoBrand;

    $brand = DemoBrand::get('brand_name');
    $tagline = DemoBrand::get('brand_tagline', '');
    $heroHeadline = DemoBrand::get('hero_headline', '');
    $heroSub = DemoBrand::get('hero_subheadline', '');
    $heroCta = DemoBrand::get('hero_cta', 'Empezar estudio');
    $heroImage = DemoBrand::get('hero_image_url', '');
    $heroImageAlt = DemoBrand::get('hero_image_alt', 'Vivienda');
    $valueTitle = DemoBrand::get('value_title', '');
    $valueBody = DemoBrand::get('value_body', '');
    $benefitsTitle = DemoBrand::get('benefits_title', '');
    $benefits = DemoBrand::get('benefits_items', []);
    $processTitle = DemoBrand::get('process_title', '');
    $processSteps = DemoBrand::get('process_steps', []);
    $testimonialsTitle = DemoBrand::get('testimonials_title', '');
    $testimonials = DemoBrand::get('testimonials_items', []);
    $faqTitle = DemoBrand::get('faq_title', '');
    $faqs = DemoBrand::get('faq_items', []);
    $footerLegal = DemoBrand::get('footer_legal', '');
    $ctaLabel = DemoBrand::currentKey() === 'crediservicios' ? 'Precalificar' : 'Estudiar hipoteca';
@endphp

<x-layouts::marketing :title="$brand">
    <livewire:demo-brand-switcher />

    <header class="absolute inset-x-0 top-0 z-20">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ route('home') }}" class="font-display text-xl tracking-tight text-white drop-shadow sm:text-2xl">
                {{ $brand }}
            </a>
            <a href="#estudio" class="rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/25">
                {{ $ctaLabel }}
            </a>
        </div>
    </header>

    <main>
        <section class="relative min-h-[100svh] overflow-hidden bg-brand-950">
            <img
                src="{{ $heroImage }}"
                alt="{{ $heroImageAlt }}"
                class="absolute inset-0 h-full w-full object-cover"
                fetchpriority="high"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-brand-950/90 via-brand-950/70 to-brand-900/35"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(212,175,106,0.18),transparent_45%)]"></div>

            <div class="relative mx-auto flex min-h-[100svh] max-w-6xl flex-col justify-end px-5 pb-16 pt-28 sm:px-8 sm:pb-20">
                <p class="animate-fade-up font-display text-4xl leading-none tracking-tight text-white sm:text-6xl lg:text-7xl">
                    {{ $brand }}
                </p>
                <h1 class="animate-fade-up-delay mt-5 max-w-2xl text-balance text-2xl font-medium leading-snug text-white/95 sm:text-3xl">
                    {{ $heroHeadline }}
                </h1>
                <p class="animate-fade-up-delay-2 mt-4 max-w-xl text-pretty text-base text-white/80 sm:text-lg">
                    {{ $heroSub }}
                </p>
                <div class="animate-fade-up-delay-2 mt-8 flex flex-wrap items-center gap-3">
                    <a href="#estudio" class="rounded-xl bg-gold px-5 py-3 text-sm font-semibold text-brand-950 transition hover:brightness-105">
                        {{ $heroCta }}
                    </a>
                    <a href="#proceso" class="rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Ver proceso
                    </a>
                </div>
                @if ($tagline)
                    <p class="mt-8 text-xs uppercase tracking-[0.22em] text-white/55">{{ $tagline }}</p>
                @endif
            </div>
        </section>

        <section class="border-b border-brand-900/10 bg-canvas px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-6xl grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">Propuesta de valor</p>
                    <h2 class="mt-3 font-display text-3xl text-brand-950 sm:text-4xl">{{ $valueTitle }}</h2>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-stone-600">{{ $valueBody }}</p>
                </div>
                <div class="rounded-2xl bg-brand-900 px-6 py-7 text-white shadow-xl shadow-brand-900/20">
                    <p class="font-display text-2xl">Estudio inicial en 6 pasos</p>
                    <p class="mt-3 text-sm text-white/75">Sin compromiso. Con validación en tiempo real y respuesta del equipo en menos de 24h laborables.</p>
                    <a href="#estudio" class="mt-6 inline-flex rounded-xl bg-gold px-4 py-2.5 text-sm font-semibold text-brand-950">Empezar ahora</a>
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-6xl">
                <h2 class="font-display text-3xl text-brand-950 sm:text-4xl">{{ $benefitsTitle }}</h2>
                <div class="mt-10 grid gap-8 sm:grid-cols-2">
                    @foreach ($benefits as $benefit)
                        <article class="border-t border-brand-900/10 pt-5">
                            <h3 class="text-lg font-semibold text-brand-900">{{ $benefit['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $benefit['body'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="proceso" class="scroll-mt-24 bg-brand-950 px-5 py-20 text-white sm:px-8">
            <div class="mx-auto max-w-6xl">
                <h2 class="font-display text-3xl sm:text-4xl">{{ $processTitle }}</h2>
                <ol class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($processSteps as $index => $step)
                        <li class="relative">
                            <p class="font-display text-4xl text-gold/80">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="mt-3 text-lg font-semibold">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm text-white/70">{{ $step['body'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="bg-canvas px-5 py-20 sm:px-8">
            <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">Conversión</p>
                    <h2 class="mt-3 font-display text-3xl text-brand-950 sm:text-4xl">
                        {{ DemoBrand::currentKey() === 'crediservicios' ? 'Completa tu precalificación' : 'Completa tu estudio hipotecario' }}
                    </h2>
                    <p class="mt-4 text-stone-600">
                        {{ DemoBrand::currentKey() === 'crediservicios'
                            ? 'Cuéntanos tu caso y te orientamos sobre BIESS, banca privada y siguientes pasos.'
                            : 'Cuéntanos tu caso y te devolvemos una orientación clara sobre viabilidad y siguientes pasos.' }}
                    </p>
                </div>
                <livewire:lead-form :key="'lead-form-'.DemoBrand::currentKey()" />
            </div>
        </section>

        <section class="bg-white px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-6xl">
                <h2 class="font-display text-3xl text-brand-950 sm:text-4xl">{{ $testimonialsTitle }}</h2>
                <div class="mt-10 grid gap-8 lg:grid-cols-3">
                    @foreach ($testimonials as $item)
                        <blockquote class="border-l-2 border-gold pl-5">
                            <p class="text-base leading-relaxed text-stone-700">&ldquo;{{ $item['quote'] ?? '' }}&rdquo;</p>
                            <footer class="mt-4">
                                <p class="font-semibold text-brand-900">{{ $item['name'] ?? '' }}</p>
                                <p class="text-sm text-stone-500">{{ $item['role'] ?? '' }}</p>
                            </footer>
                        </blockquote>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="border-t border-brand-900/10 bg-canvas px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-3xl">
                <h2 class="font-display text-3xl text-brand-950 sm:text-4xl">{{ $faqTitle }}</h2>
                <div class="mt-8 divide-y divide-brand-900/10">
                    @foreach ($faqs as $faq)
                        <details class="group py-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-brand-900">
                                <span>{{ $faq['q'] ?? '' }}</span>
                                <span class="text-brand-700 transition group-open:rotate-45">+</span>
                            </summary>
                            <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $faq['a'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-brand-950 px-5 py-10 text-white/70 sm:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-display text-xl text-white">{{ $brand }}</p>
                <p class="mt-2 max-w-xl text-sm">{{ $footerLegal }}</p>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-white/50 hover:text-white">Acceso equipo</a>
        </div>
    </footer>
</x-layouts::marketing>
