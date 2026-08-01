<x-layouts::marketing title="Política de privacidad">
    <main class="mx-auto max-w-3xl px-5 py-16 sm:px-8">
        <a href="{{ route('home') }}" class="text-sm text-brand-700 hover:underline">← Volver</a>
        <h1 class="mt-6 font-display text-4xl text-brand-950">Política de privacidad</h1>
        <div class="prose prose-stone mt-8 max-w-none text-sm leading-relaxed text-stone-700">
            <p>
                {{ config('services.leads.brand_name') }} trata los datos facilitados en el formulario de estudio hipotecario
                con la finalidad de gestionar tu solicitud, contactarte y, si lo autorizas, enviarte comunicaciones comerciales.
            </p>
            <p>
                Base jurídica: ejecución de medidas precontractuales a petición del interesado y, para marketing, consentimiento.
            </p>
            <p>
                Puedes ejercer tus derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad
                escribiendo a {{ config('mail.from.address') }}.
            </p>
            <p>
                Este texto es una base editable. Sustitúyelo por la versión legal definitiva de tu asesoría antes de publicar.
            </p>
        </div>
    </main>
</x-layouts::marketing>
