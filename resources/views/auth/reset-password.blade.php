<x-guest-layout title="Kata sandi baru">
    <h1 class="font-display text-3xl font-bold tracking-tight text-white">Kata sandi baru</h1>
    <p class="mt-2 text-sm text-slate-300">Minimal 8 karakter. Setelah disimpan, Anda diarahkan ke halaman masuk.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-auth.field name="email" label="Email" type="email" :value="old('email', $email)" autocomplete="username" readonly />
        <x-auth.field name="password" label="Kata sandi baru" type="password" autocomplete="new-password" autofocus />
        <x-auth.field name="password_confirmation" label="Ulangi kata sandi baru" type="password" autocomplete="new-password" />

        <button type="submit"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
            Simpan kata sandi
        </button>
    </form>
</x-guest-layout>

