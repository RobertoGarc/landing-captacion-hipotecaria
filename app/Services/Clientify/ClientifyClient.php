<?php

namespace App\Services\Clientify;

use App\Models\Lead;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ClientifyClient
{
    public function enabled(): bool
    {
        return filled(config('services.clientify.token'));
    }

    /**
     * @return array<string, mixed>
     */
    public function createContactFromLead(Lead $lead): array
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Clientify API token is not configured.');
        }

        $payload = $this->payloadFromLead($lead);

        try {
            $response = $this->http()
                ->post('/contacts/', $payload)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            throw new RuntimeException(
                'Clientify contact creation failed: '.$exception->response?->body(),
                previous: $exception,
            );
        }

        return is_array($response) ? $response : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadFromLead(Lead $lead): array
    {
        $nameParts = preg_split('/\s+/', trim($lead->name), 2) ?: [$lead->name];

        return [
            'first_name' => $nameParts[0] ?? $lead->name,
            'last_name' => $nameParts[1] ?? '',
            'email' => $lead->email,
            'phone' => $lead->phone,
            'contact_source' => $lead->utm_source ?: 'Landing hipotecaria',
            'status' => 'cold-lead',
            'addresses' => [
                [
                    'state' => $lead->province,
                    'country' => $lead->currency === 'USD' ? 'Ecuador' : 'Spain',
                    'type' => 1,
                ],
            ],
            'custom_fields' => [
                ['field' => 'purpose', 'value' => $lead->purpose->label()],
                ['field' => 'currency', 'value' => $lead->currency],
                ['field' => 'property_price', 'value' => (string) $lead->property_price],
                ['field' => 'financing_amount', 'value' => (string) $lead->financing_amount],
                ['field' => 'savings_amount', 'value' => (string) $lead->savings_amount],
                ['field' => 'holders_count', 'value' => (string) $lead->holders_count],
                ['field' => 'employment_status', 'value' => $lead->employment_status->label()],
                ['field' => 'monthly_income', 'value' => (string) $lead->monthly_income],
                ['field' => 'monthly_debts', 'value' => (string) $lead->monthly_debts],
                ['field' => 'utm_campaign', 'value' => (string) $lead->utm_campaign],
            ],
            'description' => $this->descriptionFromLead($lead),
            'tags' => ['landing-hipotecaria', 'lead-web'],
        ];
    }

    public function descriptionFromLead(Lead $lead): string
    {
        return collect([
            'Lead desde landing hipotecaria',
            'Objetivo: '.$lead->purpose->label(),
            'Provincia: '.$lead->province,
            'Precio inmueble: '.$lead->money($lead->property_price),
            'Financiación: '.$lead->money($lead->financing_amount),
            'Ahorros: '.$lead->money($lead->savings_amount),
            'Titulares: '.$lead->holders_count,
            'Empleo: '.$lead->employment_status->label(),
            'Ingresos mensuales: '.$lead->money($lead->monthly_income),
            'Deudas mensuales: '.$lead->money($lead->monthly_debts),
            'UTM: '.collect([
                $lead->utm_source,
                $lead->utm_medium,
                $lead->utm_campaign,
            ])->filter()->implode(' / '),
        ])->implode("\n");
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl((string) config('services.clientify.base_url'))
            ->withHeaders([
                'Authorization' => 'Token '.config('services.clientify.token'),
                'Accept' => 'application/json',
            ])
            ->timeout((int) config('services.clientify.timeout', 15))
            ->acceptJson();
    }
}
