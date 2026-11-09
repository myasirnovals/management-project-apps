<x-guest-layout title="Lupa kata sandi">
    <h1 class="font-display text-3xl font-bold tracking-tight text-white">Lupa kata sandi</h1>
    <p class="mt-2 text-sm text-slate-300">
        Masukkan email akun Anda. Tautan untuk membuat kata sandi baru dikirim ke alamat itu dan berlaku {{ config('auth.passwords.users.expire') }} menit.
    </p>

    @if (session('status'))
        <div role="status" class="mt-6 rounded-lg border border-indigo-400/40 bg-indigo-500/10 px-4 py-3 text-sm text-indigo-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth.field name="email" label="Email" type="email" autocomplete="username" autofocus />

        <button type="submit"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
            Kirim tautan
        </button>
    </form>

    <p class="mt-6 text-sm text-slate-400">
        Ingat kata sandinya?
        <a href="{{ route('login') }}" class="text-indigo-300 underline-offset-2 hover:text-indigo-200 hover:underline">Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>

