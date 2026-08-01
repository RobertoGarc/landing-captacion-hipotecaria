<?php

namespace App\Services;

use App\Jobs\SyncLeadToClientify;
use App\Mail\LeadConfirmationMail;
use App\Mail\NewLeadNotificationMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

class LeadIntakeService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): Lead
    {
        $lead = Lead::query()->create($data);

        SyncLeadToClientify::dispatch($lead);

        Mail::to($lead->email)->queue(new LeadConfirmationMail($lead));

        $internalRecipients = collect(explode(',', (string) config('services.leads.notify_emails')))
            ->map(fn (string $email) => trim($email))
            ->filter()
            ->all();

        if ($internalRecipients !== []) {
            Mail::to($internalRecipients)->queue(new NewLeadNotificationMail($lead));
        }

        return $lead;
    }
}
