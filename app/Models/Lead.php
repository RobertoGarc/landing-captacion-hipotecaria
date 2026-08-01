<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use App\Enums\LeadStatus;
use App\Enums\MortgagePurpose;
use App\Support\Money;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property LeadStatus $status
 * @property MortgagePurpose $purpose
 * @property int|null $property_price
 * @property string $province
 * @property string $currency
 * @property int|null $financing_amount
 * @property int|null $savings_amount
 * @property int $holders_count
 * @property EmploymentStatus $employment_status
 * @property int $monthly_income
 * @property int $monthly_debts
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property bool $privacy_accepted
 * @property bool $marketing_accepted
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $gclid
 * @property string|null $fbclid
 * @property string|null $referrer
 * @property string|null $landing_url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $clientify_id
 * @property Carbon|null $clientify_synced_at
 * @property string|null $clientify_error
 * @property string|null $admin_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'status',
        'purpose',
        'property_price',
        'province',
        'currency',
        'financing_amount',
        'savings_amount',
        'holders_count',
        'employment_status',
        'monthly_income',
        'monthly_debts',
        'name',
        'email',
        'phone',
        'privacy_accepted',
        'marketing_accepted',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
        'referrer',
        'landing_url',
        'ip_address',
        'user_agent',
        'clientify_id',
        'clientify_synced_at',
        'clientify_error',
        'admin_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'purpose' => MortgagePurpose::class,
            'employment_status' => EmploymentStatus::class,
            'property_price' => 'integer',
            'financing_amount' => 'integer',
            'savings_amount' => 'integer',
            'holders_count' => 'integer',
            'monthly_income' => 'integer',
            'monthly_debts' => 'integer',
            'privacy_accepted' => 'boolean',
            'marketing_accepted' => 'boolean',
            'clientify_synced_at' => 'datetime',
        ];
    }

    public function money(?int $amount): string
    {
        return Money::format($amount, $this->currency);
    }

    public function isSyncedToClientify(): bool
    {
        return filled($this->clientify_id) && $this->clientify_synced_at !== null;
    }

    public function debtToIncomeRatio(): ?float
    {
        if ($this->monthly_income <= 0) {
            return null;
        }

        return round(($this->monthly_debts / $this->monthly_income) * 100, 1);
    }

    public function loanToValueRatio(): ?float
    {
        if (! $this->property_price || $this->property_price <= 0 || ! $this->financing_amount) {
            return null;
        }

        return round(($this->financing_amount / $this->property_price) * 100, 1);
    }
}
