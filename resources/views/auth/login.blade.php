<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            {{ $appBrandName ?? 'Capstone Project Manager' }}
        </h1>
        <p class="mt-1.5 text-sm text-slate-600 dark:text-slate-300">
            Masuk untuk mengelola proyek Agile, sprint, dan pelacakan issue tim.
        </p>
    </div>

    <div class="app-card p-6 sm:p-7 shadow-sm">
        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="form-label">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="nama@organisasi.id"
                    class="form-input">
            </div>

            <div>
                <label for="password" class="form-label">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                    class="form-input">
            </div>

            <div class="pt-1">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none min-h-11 sm:min-h-8">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-700 focus:ring-blue-600">
                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Ingat sesi saya di perangkat ini</span>
                </label>
            </div>

            <div class="pt-1">
                <button type="submit" class="btn-primary w-full">
                    Masuk ke Workspace
                </button>
            </div>
        </form>

        @if (app()->environment(['local', 'testing']))
            <details class="mt-5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/70">
                <summary class="px-3.5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 cursor-pointer select-none flex items-center justify-between min-h-11 sm:min-h-9">
                    <span>Akun Demo Pengujian Lokal</span>
                    <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400">Isi cepat</span>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 border-t border-slate-200 dark:border-slate-700 space-y-2">
                    <p class="text-xs text-slate-600 dark:text-slate-300">Pilih peran untuk mengisi kredensial contoh secara otomatis:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <button type="button" onclick="fillDemoLogin('admin@project.local')" class="text-left px-2.5 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs transition-colors min-h-11 sm:min-h-9 cursor-pointer">
                            <span class="font-semibold block text-slate-900 dark:text-white">Project Manager</span>
                            <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300">admin@project.local</span>
                        </button>
                        <button type="button" onclick="fillDemoLogin('po@project.local')" class="text-left px-2.5 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs transition-colors min-h-11 sm:min-h-9 cursor-pointer">
                            <span class="font-semibold block text-slate-900 dark:text-white">Product Owner</span>
                            <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300">po@project.local</span>
                        </button>
                        <button type="button" onclick="fillDemoLogin('sm@project.local')" class="text-left px-2.5 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs transition-colors min-h-11 sm:min-h-9 cursor-pointer">
                            <span class="font-semibold block text-slate-900 dark:text-white">Scrum Master</span>
                            <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300">sm@project.local</span>
                        </button>
                        <button type="button" onclick="fillDemoLogin('dev1@project.local')" class="text-left px-2.5 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs transition-colors min-h-11 sm:min-h-9 cursor-pointer">
                            <span class="font-semibold block text-slate-900 dark:text-white">Developer</span>
                            <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300">dev1@project.local</span>
                        </button>
                        <button type="button" onclick="fillDemoLogin('viewer@project.local')" class="sm:col-span-2 text-left px-2.5 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs transition-colors min-h-11 sm:min-h-9 cursor-pointer">
                            <span class="font-semibold block text-slate-900 dark:text-white">Viewer (Read-only)</span>
                            <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300">viewer@project.local</span>
                        </button>
                    </div>
                </div>
            </details>
            <script>
                function fillDemoLogin(email) {
                    const emailEl = document.getElementById('email');
                    const passEl = document.getElementById('password');
                    if (emailEl) emailEl.value = email;
                    if (passEl) passEl.value = 'password123';
                    emailEl?.focus();
                }
            </script>
        @endif
    </div>
</x-guest-layout>
