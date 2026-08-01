@php
    $gtmId = config('services.analytics.gtm_id');
    $currency = \App\Support\DemoBrand::currency();
@endphp

@if ($gtmId)
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe>
    </noscript>
@endif

<script>
    document.addEventListener('lead-submitted', function () {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'lead_submitted', lead_type: 'mortgage_study' });

        if (typeof gtag === 'function') {
            gtag('event', 'generate_lead', { currency: @json($currency), value: 1 });
        }

        if (typeof fbq === 'function') {
            fbq('track', 'Lead');
        }
    });
</script>
