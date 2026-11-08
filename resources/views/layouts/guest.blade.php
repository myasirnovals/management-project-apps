<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <script>
        try {
            var savedScheme = localStorage.getItem('color-scheme');
            if (savedScheme === 'light' || savedScheme === 'dark') {
                document.querySelector('meta[name="color-scheme"]').content = savedScheme;
            }
        } catch (e) {}
    </script>

    <title>{{ $appBrandName ?? config('app.name', 'Capstone Project Manager') }} - Masuk</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased flex flex-col">
    <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 pt-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-blue-700 text-white font-mono text-xs font-bold tracking-tight">
                {{ collect(explode(' ', $appBrandName ?? 'Capstone Project Manager'))->filter()->take(3)->map(fn ($w) => strtoupper(substr($w, 0, 1)))->implode('') ?: 'CPM' }}
            </span>
            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $appOrgName ?? 'Workspace Manajemen Proyek' }}</span>
        </div>

        <button type="button" onclick="toggleColorScheme()" class="btn-secondary !px-3" title="Ubah tema tampilan">
            <span class="inline-flex items-center gap-1.5 dark:hidden">
                <svg class="w-4 h-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <span class="text-xs font-medium">Gelap</span>
                <span class="sr-only">Ubah ke tema gelap</span>
            </span>
            <span class="hidden dark:inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-xs font-medium">Terang</span>
                <span class="sr-only">Ubah ke tema terang</span>
            </span>
        </button>
    </header>

    <main class="flex-1 flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md mx-auto">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
