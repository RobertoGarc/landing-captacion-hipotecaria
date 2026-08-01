<?php

use App\Jobs\SyncLeadToClientify;
use App\Models\Lead;
use App\Services\Clientify\ClientifyClient;
use Illuminate\Support\Facades\Http;

test('sincroniza el lead con Clientify cuando hay token', function () {
    config(['services.clientify.token' => 'test-token']);

    Http::fake([
        'api.clientify.net/v1/contacts/*' => Http::response(['id' => 98765], 201),
    ]);

    $lead = Lead::factory()->create();

    (new SyncLeadToClientify($lead))->handle(app(ClientifyClient::class));

    $lead->refresh();

    expect($lead->clientify_id)->toBe('98765')
        ->and($lead->clientify_synced_at)->not->toBeNull()
        ->and($lead->clientify_error)->toBeNull();

    Http::assertSent(function ($request) use ($lead) {
        return str_contains($request->url(), '/contacts/')
            && $request['email'] === $lead->email
            && $request->hasHeader('Authorization', 'Token test-token');
    });
});

test('no llama a Clientify si no hay token configurado', function () {
    config(['services.clientify.token' => null]);

    Http::fake();

    $lead = Lead::factory()->create();

    (new SyncLeadToClientify($lead))->handle(app(ClientifyClient::class));

    Http::assertNothingSent();

    expect($lead->fresh()->clientify_id)->toBeNull();
});
