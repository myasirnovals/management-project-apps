<x-guest-layout>
    <div class="text-center mb-8">
        <!-- Logo -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 shadow-xl shadow-indigo-500/25 ring-1 ring-white/20 mb-4">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-white">AgilePM Workspace</h2>
        <p class="mt-2 text-sm text-slate-400">
            Sistem Manajemen Proyek Berbasis Agile & Scrum
        </p>
    </div>

    <!-- Login Card -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl shadow-black/50">
        <!-- Notification Message -->
        @if (session('status'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email', 'admin@project.local') }}" required autofocus
                        class="block w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Kata Sandi
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" value="password123" required
                        class="block w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                    <span class="ml-2 text-xs text-slate-400">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-600/30 transition duration-150">
                    Masuk ke Workspace
                </button>
            </div>
        </form>

        <!-- Quick Credentials Switcher (Helper for instant testing) -->
        <div class="mt-6 pt-6 border-t border-slate-800">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Akun Bawaan (Klik untuk Isi Cepat):</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="fillCredentials('admin@project.local', 'password123')"
                    class="p-2 text-left rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition">
                    <div class="font-semibold text-indigo-400">Project Manager</div>
                    <div class="text-[11px] text-slate-400 truncate">admin@project.local</div>
                </button>
                <button type="button" onclick="fillCredentials('po@project.local', 'password123')"
                    class="p-2 text-left rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition">
                    <div class="font-semibold text-blue-400">Product Owner</div>
                    <div class="text-[11px] text-slate-400 truncate">po@project.local</div>
                </button>
                <button type="button" onclick="fillCredentials('yessi@project.local', 'password123')"
                    class="p-2 text-left rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition">
                    <div class="font-semibold text-emerald-400">Frontend Dev</div>
                    <div class="text-[11px] text-slate-400 truncate">yessi@project.local</div>
                </button>
                <button type="button" onclick="fillCredentials('eprian@project.local', 'password123')"
                    class="p-2 text-left rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition">
                    <div class="font-semibold text-purple-400">Backend Dev</div>
                    <div class="text-[11px] text-slate-400 truncate">eprian@project.local</div>
                </button>
            </div>
        </div>

        <!-- Note about no registration -->
        <div class="mt-6 flex items-start gap-2.5 p-3 rounded-xl bg-slate-950/40 border border-slate-800 text-xs text-slate-400">
            <svg class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>
                <strong>Registrasi Publik Ditiadakan:</strong> Akun anggota tim dibuat dan dikelola langsung oleh Admin / Project Manager di menu <em>Kelola Tim</em>.
            </span>
        </div>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
