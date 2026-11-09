<x-guest-layout title="Masuk">
    <h1 class="font-display text-3xl font-bold tracking-tight text-white">Masuk</h1>
    <p class="mt-2 text-sm text-slate-300">Gunakan email dan kata sandi dari Project Manager.</p>

    @if (session('status'))
        <div role="status" class="mt-6 rounded-lg border border-indigo-400/40 bg-indigo-500/10 px-4 py-3 text-sm text-indigo-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth.field name="email" label="Email" type="email" autocomplete="username" autofocus />

        <x-auth.field name="password" label="Kata sandi" type="password" autocomplete="current-password">
            <x-slot name="aside">
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-300 underline-offset-2 hover:text-indigo-200 hover:underline">
                    Lupa kata sandi?
                </a>
            </x-slot>
        </x-auth.field>

        <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-600 bg-slate-900">
            Ingat saya di perangkat ini
        </label>

        <button type="submit"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 focus-visible:outline-indigo-300">
            Masuk
        </button>
    </form>
</x-guest-layout>
