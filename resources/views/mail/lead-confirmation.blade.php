<x-mail::message>
# Hemos recibido tu solicitud

Hola {{ $lead->name }},

Gracias por confiar en {{ config('services.leads.brand_name') }}. Ya tenemos tu estudio inicial y un especialista revisará tu caso.

**Resumen rápido**
- Objetivo: {{ $lead->purpose->label() }}
- Provincia: {{ $lead->province }}
- Financiación solicitada: {{ $lead->money($lead->financing_amount) }}

Te contactaremos en breve para ampliar detalles o pedirte documentación si procede.

Un saludo,<br>
{{ config('services.leads.brand_name') }}
</x-mail::message>
