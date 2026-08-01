<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.home')->name('home');

Route::view('/privacidad', 'landing.privacy')->name('privacy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/admin/leads')->name('dashboard');

    Route::livewire('admin/leads', 'pages::admin.leads-index')->name('admin.leads.index');
    Route::livewire('admin/leads/{lead}', 'pages::admin.lead-show')->name('admin.leads.show');
    Route::livewire('admin/contenido', 'pages::admin.content-editor')->name('admin.content.edit');
});

require __DIR__.'/settings.php';
