@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'url' => null,
    'type' => 'website'
])

@php
    $title = $title ?? config('app.name');
    $description = $description ?? 'Магазин скриптов и индикаторов для торговли';
    $image = $image ?? asset('images/og-default.jpg');
    $url = $url ?? url()->current();
    $siteName = config('app.name');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ Str::limit(strip_tags($description), 160) }}">
<link rel="canonical" href="{{ $url }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($description), 160) }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="ru_RU">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($description), 160) }}">
<meta name="twitter:image" content="{{ $image }}">
