<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'GerakIn' }} - Fitness & Workout Tracker</title>

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="GerakIn">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicon.png">

    <!-- Fonts & Vite Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between selection:bg-blue-600 selection:text-white pb-36 antialiased" x-data="{ globalStartModal: false }">
    <!-- Top Header (Clean Light with Soft Blue accent) -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 py-3">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <img src="/icons/icon-72x72.png" alt="GerakIn Logo" class="w-9 h-9 rounded-xl shadow-xs group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="font-display font-extrabold text-xl tracking-tight flex items-center gap-1 text-slate-900">
                        <span>Gerak</span><span class="text-blue-600">In</span>
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium -mt-1 hidden sm:block">Workout & Habit Tracker</span>
                </div>
            </a>

            <div class="flex items-center gap-2" x-data="{ isOffline: !navigator.onLine, installAvailable: false }" x-init="
                window.addEventListener('offline', () => isOffline = true);
                window.addEventListener('online', () => isOffline = false);
                window.addEventListener('pwa-installable', () => installAvailable = true);
            ">
                <!-- Offline Badge -->
                <div x-show="isOffline" x-cloak class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Offline (Mode Antrean)</span>
                </div>

                <!-- PWA Install Button -->
                <button @click="window.triggerPwaInstall()" x-show="installAvailable" x-cloak class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-semibold transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Pasang App</span>
                </button>

                <!-- Exercise Library Link -->
                <a href="{{ route('exercises.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors" title="Daftar Gerakan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </a>

                <!-- Running / Strava GPS Tracker Link -->
                <a href="{{ route('runs.index') }}" class="p-2 rounded-xl {{ request()->routeIs('runs*') ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900' }} transition-colors" title="Lari & GPS Tracker">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </a>

                <!-- User Profile Avatar -->
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 p-1 pl-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                    <span class="text-xs font-semibold text-slate-700 hidden sm:inline">{{ Auth::user()->name ?? 'User' }}</span>
                    <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-xs">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </a>
            </div>
        </div>
    </header>

    <!-- Global Toast Alerts -->
    <div class="max-w-4xl mx-auto px-4 w-full mt-3">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="flex items-center justify-between p-3.5 mb-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        @if (session('info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="flex items-center justify-between p-3.5 mb-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button @click="show = false" class="text-blue-500 hover:text-blue-700">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="flex items-center justify-between p-3.5 mb-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
            </div>
        @endif
    </div>

    <!-- Active Workout Persistent Bar (if session exists and user is not in workouts.active) -->
    @php
        $currentActiveSession = Auth::check()
            ? Auth::user()->workoutSessions()->where('is_completed', false)->latest('started_at')->first()
            : null;
    @endphp

    @if ($currentActiveSession && !request()->routeIs('workouts.active'))
        <div class="sticky top-16 z-30 max-w-4xl mx-auto px-4 w-full mb-4">
            <div class="p-3 sm:p-3.5 rounded-2xl bg-blue-600 text-white shadow-md flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[9px] sm:text-[10px] uppercase tracking-wider font-bold text-blue-100 truncate">
                            {{ $currentActiveSession->isTimerActive() ? 'Sesi Latihan Berlangsung' : 'Perencanaan Sesi Latihan' }}
                        </div>
                        <div class="font-bold text-xs sm:text-sm truncate">{{ $currentActiveSession->title }}</div>
                    </div>
                </div>
                <a href="{{ route('workouts.active', $currentActiveSession->id) }}" class="shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-white text-blue-600 font-bold text-xs hover:bg-blue-50 shadow-xs transition-transform active:scale-95 flex items-center gap-1">
                    <span>{{ $currentActiveSession->isTimerActive() ? 'Lanjutkan' : 'Buka' }}</span>
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="max-w-4xl mx-auto px-4 w-full flex-grow pb-28 sm:pb-24">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- FLOATING GLASSMORPHISM BOTTOM NAVIGATION BAR (Finding #002) -->
    <div class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md pointer-events-none">
        <nav class="floating-glass rounded-full px-3 py-2 pointer-events-auto flex items-center justify-between">
            <!-- 1. Beranda -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 px-3 rounded-full transition-all {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[9px] font-semibold mt-0.5">Beranda</span>
            </a>

            <!-- 2. Jadwal / Routines -->
            <a href="{{ route('routines.index') }}" class="flex flex-col items-center justify-center py-1 px-3 rounded-full transition-all {{ request()->routeIs('routines*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-[9px] font-semibold mt-0.5">Jadwal</span>
            </a>

            <!-- 3. CENTER BUTTON: + Mulai (Pilih Gym / Lari GPS) -->
            <button type="button" @click="globalStartModal = true" class="w-11 h-11 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all group" title="Mulai Sesi Baru">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </button>

            <!-- 4. Analitik / Statistik -->
            <a href="{{ route('analytics.index') }}" class="flex flex-col items-center justify-center py-1 px-3 rounded-full transition-all {{ request()->routeIs('analytics*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-[9px] font-semibold mt-0.5">Statistik</span>
            </a>

            <!-- 5. Profil -->
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center justify-center py-1 px-3 rounded-full transition-all {{ request()->routeIs('profile*') ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[9px] font-semibold mt-0.5">Profil</span>
            </a>
        </nav>
    </div>

    <!-- Global Quick Start Session Modal (Gym vs Running) -->
    <div x-show="globalStartModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-3 sm:p-4">
        <div @click.away="globalStartModal = false" class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-display font-black text-lg text-slate-900">Mulai Sesi Baru</h3>
                    <p class="text-xs text-slate-500">Pilih jenis aktivitas latihanmu</p>
                </div>
                <button type="button" @click="globalStartModal = false" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2.5">
                <!-- Gym Workout Option -->
                <a href="{{ route('workouts.start') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all group">
                    <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-display font-bold text-sm text-slate-900 group-hover:text-blue-700">Latihan Gym & Beban</div>
                        <div class="text-[11px] text-slate-500 leading-tight mt-0.5">Catat set, repetisi, beban kg, dan timer istirahat</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- Running / Strava Mode Option -->
                <a href="{{ route('runs.track') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition-all group">
                    <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-display font-bold text-sm text-slate-900 group-hover:text-emerald-700">Lari GPS (Strava Mode)</div>
                        <div class="text-[11px] text-slate-500 leading-tight mt-0.5">Lacak rute peta, durasi, pace min/km, dan split KM</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="pt-2 text-center border-t border-slate-100">
                <a href="{{ route('runs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800">
                    <span>Lihat Riwayat & Rekor Lari</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
