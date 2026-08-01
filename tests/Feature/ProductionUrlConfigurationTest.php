<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;

test('en produccion fuerza https y la URL externa de Render', function () {
    $this->app['env'] = 'production';

    config(['app.env' => 'production']);

    putenv('RENDER_EXTERNAL_URL=https://landing-hipotecaria.onrender.com');
    $_ENV['RENDER_EXTERNAL_URL'] = 'https://landing-hipotecaria.onrender.com';
    $_SERVER['RENDER_EXTERNAL_URL'] = 'https://landing-hipotecaria.onrender.com';

    $provider = new AppServiceProvider($this->app);
    $provider->boot();

    expect(URL::formatScheme(true))->toBe('https://')
        ->and(url('/'))->toStartWith('https://landing-hipotecaria.onrender.com');
});
