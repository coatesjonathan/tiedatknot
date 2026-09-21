<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f2efeb">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $site['coupleNames'] ?? 'Wedding Invitation' }}">
    <meta property="og:description" content="You are cordially invited to celebrate the wedding of {{ $site['coupleNames'] ?? 'Jenny & Jonny' }} in {{ $site['locationLabel'] ?? 'Granada, Spain' }}.">
    <meta property="og:image" content="{{ asset('og-image.svg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $site['coupleNames'] ?? 'Wedding Invitation' }}">
    <meta property="twitter:description" content="You are cordially invited to celebrate the wedding of {{ $site['coupleNames'] ?? 'Jenny & Jonny' }} in {{ $site['locationLabel'] ?? 'Granada, Spain' }}.">
    <meta property="twitter:image" content="{{ asset('og-image.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Jost:wght@300;400;500&family=Pinyon+Script&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-inertia::head />
</head>
<body class="bg-sand font-sans text-ink antialiased">
    <x-inertia::app />
</body>
</html>
