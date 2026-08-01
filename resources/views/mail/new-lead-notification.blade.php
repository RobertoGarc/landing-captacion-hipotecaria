<x-mail::message>
# Nuevo lead hipotecario

Se ha registrado una nueva solicitud desde la landing.

**Contacto**
- Nombre: {{ $lead->name }}
- Email: {{ $lead->email }}
- Teléfono: {{ $lead->phone }}

**Operación**
- Objetivo: {{ $lead->purpose->label() }}
- Provincia: {{ $lead->province }}
- Precio inmueble: {{ $lead->money($lead->property_price) }}
- Financiación: {{ $lead->money($lead->financing_amount) }}
- Ahorros: {{ $lead->money($lead->savings_amount) }}
- Titulares: {{ $lead->holders_count }}
- Empleo: {{ $lead->employment_status->label() }}
- Ingresos: {{ $lead->money($lead->monthly_income) }}
- Deudas: {{ $lead->money($lead->monthly_debts) }}

**Atribución**
- UTM: {{ collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign])->filter()->implode(' / ') ?: '—' }}

<x-mail::button :url="route('admin.leads.show', $lead)">
Ver lead en el panel
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
