<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    @include('layouts.partials.head')
    <title>{{ $attributes->get('title', 'Masuk') }} - AgilePM</title>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased lg:grid lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">
    <aside class="relative hidden overflow-hidden lg:flex lg:flex-col lg:justify-between lg:p-12">
        <img src="{{ asset('images/hutan-pagi.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-slate-950/20"></div>

        <div class="relative flex items-center gap-3">
            <x-brand-mark size="h-11 w-11" />
            <span class="font-display text-2xl font-bold tracking-tight text-white">AgilePM Workspace</span>
        </div>

        <div class="relative max-w-md">
            <p class="font-display text-3xl font-semibold leading-snug text-white">
                Backlog, sprint, dan rilis tim dikelola dari satu papan kerja.
            </p>
            <p class="mt-4 text-sm text-slate-200">
                Akun dibuat oleh Project Manager. Setiap peran melihat menu dan data sesuai tanggung jawabnya.
            </p>
            <p class="mt-10 text-xs text-slate-300">
                Foto latar: Sebastian Unrau, Unsplash
            </p>
        </div>
    </aside>

    <main class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <x-brand-mark size="h-10 w-10" />
                <span class="font-display text-xl font-bold tracking-tight text-white">AgilePM Workspace</span>
            </div>

            {{ $slot }}
        </div>
    </main>
</body>
</html>
