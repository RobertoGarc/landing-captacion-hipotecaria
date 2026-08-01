<?php

test('returns a successful response', function () {
    $this->seed(\Database\Seeders\SiteSettingSeeder::class);

    $response = $this->get(route('home'));

    $response->assertOk();
});
