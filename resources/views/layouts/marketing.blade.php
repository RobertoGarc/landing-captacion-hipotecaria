@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ \App\Support\DemoBrand::theme() }}">
    <head>
        @include('partials.head', ['title' => $title])
        @include('partials.analytics-head')
        <style>
            [x-cloak], [wire\:cloak] { display: none !important; }
        </style>
    </head>
    <body class="min-h-screen bg-canvas font-sans text-brand-950 antialiased">
        @include('partials.analytics-body')

        {{ $slot }}

        @fluxScripts
    </body>
</html>
