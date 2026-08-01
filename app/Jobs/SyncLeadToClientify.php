<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\Clientify\ClientifyClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncLeadToClientify implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Lead $lead) {}

    public function handle(ClientifyClient $clientify): void
    {
        if (! $clientify->enabled()) {
            Log::info('Clientify sync skipped: token not configured.', [
                'lead_id' => $this->lead->id,
            ]);

            return;
        }

        if ($this->lead->isSyncedToClientify()) {
            return;
        }

        $response = $clientify->createContactFromLead($this->lead);

        $this->lead->forceFill([
            'clientify_id' => isset($response['id']) ? (string) $response['id'] : null,
            'clientify_synced_at' => now(),
            'clientify_error' => null,
        ])->save();
    }

    public function failed(?Throwable $exception): void
    {
        $this->lead->forceFill([
            'clientify_error' => $exception?->getMessage(),
        ])->save();
    }
}
