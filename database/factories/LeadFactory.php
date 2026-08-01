<?php

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use App\Enums\LeadStatus;
use App\Enums\MortgagePurpose;
use App\Models\Lead;
use App\Support\EcuadorProvinces;
use App\Support\SpanishProvinces;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyPrice = fake()->numberBetween(120000, 650000);
        $savings = fake()->numberBetween(15000, 120000);
        $financing = max(50000, $propertyPrice - $savings);

        return [
            'status' => LeadStatus::New,
            'purpose' => fake()->randomElement(MortgagePurpose::cases()),
            'property_price' => $propertyPrice,
            'province' => fake()->randomElement(array_keys(SpanishProvinces::all())),
            'currency' => 'EUR',
            'financing_amount' => $financing,
            'savings_amount' => $savings,
            'holders_count' => fake()->numberBetween(1, 2),
            'employment_status' => fake()->randomElement(EmploymentStatus::cases()),
            'monthly_income' => fake()->numberBetween(1800, 6500),
            'monthly_debts' => fake()->numberBetween(0, 800),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '6'.fake()->numerify('########'),
            'privacy_accepted' => true,
            'marketing_accepted' => fake()->boolean(),
            'utm_source' => fake()->optional()->randomElement(['google', 'meta', 'email', 'referral']),
            'utm_medium' => fake()->optional()->randomElement(['cpc', 'organic', 'social', 'email']),
            'utm_campaign' => fake()->optional()->slug(),
            'utm_term' => null,
            'utm_content' => null,
            'gclid' => null,
            'fbclid' => null,
            'referrer' => fake()->optional()->url(),
            'landing_url' => config('app.url'),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function ecuador(): static
    {
        return $this->state(fn () => [
            'province' => fake()->randomElement(array_keys(EcuadorProvinces::all())),
            'currency' => 'USD',
            'phone' => '09'.fake()->numerify('########'),
        ]);
    }

    public function synced(): static
    {
        return $this->state(fn () => [
            'clientify_id' => (string) fake()->numberBetween(1000, 99999),
            'clientify_synced_at' => now(),
            'clientify_error' => null,
        ]);
    }
}
