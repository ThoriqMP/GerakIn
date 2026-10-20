<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>GerakIn - Pelacak Olahraga Gym, Angkat Beban & Lari GPS All-in-One</title>
    <meta name="description" content="Aplikasi pencatat latihan beban (gym) dan pelacak lari GPS ala Strava dengan pace real-time, muscle heatmap interaktif, dan dukungan offline PWA tanpa iklan.">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <!-- Fonts & Vite Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- 1. TOP NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                <img src="/icons/icon-72x72.png" alt="GerakIn Logo" class="w-9 h-9 rounded-xl shadow-xs group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="font-display font-extrabold text-xl sm:text-2xl tracking-tight text-slate-900 flex items-center">
                        <span>Gerak</span><span class="text-blue-600">In</span>
                    </span>
                    <span class="text-[9px] text-slate-500 font-semibold tracking-wider uppercase -mt-1 hidden sm:block">Workout & Running</span>
                </div>
            </a>

            <!-- Desktop Nav Links (Clean, No Wrapping) -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm font-semibold text-slate-600">
                <a href="#fitur-utama" class="whitespace-nowrap hover:text-blue-600 transition-colors">Fitur</a>
                <a href="#strava-running" class="whitespace-nowrap hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <span>Lari GPS</span>
                    <span class="px-1.5 py-0.5 text-[9px] bg-blue-100 text-blue-700 rounded-md font-extrabold">Baru</span>
                </a>
                <a href="#gym-logger" class="whitespace-nowrap hover:text-blue-600 transition-colors">Gym & Beban</a>
                <a href="#muscle-heatmap" class="whitespace-nowrap hover:text-blue-600 transition-colors">Heatmap Otot</a>
                <a href="#pwa-offline" class="whitespace-nowrap hover:text-blue-600 transition-colors">Offline PWA</a>
                <a href="#faq" class="whitespace-nowrap hover:text-blue-600 transition-colors">FAQ</a>
            </nav>

            <!-- Auth CTAs (Single-Line, Non-wrapping Buttons) -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="whitespace-nowrap px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all flex items-center gap-1.5">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('demo.login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors border border-slate-200 whitespace-nowrap" title="Coba langsung tanpa registrasi">
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <span>Demo 1-Klik</span>
                    </a>

                    <a href="{{ route('login') }}" class="whitespace-nowrap px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 transition-colors">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}" class="whitespace-nowrap px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                        Daftar Gratis
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors" aria-label="Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Nav -->
        <div x-show="mobileMenu" x-cloak @click.away="mobileMenu = false" class="lg:hidden bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 py-4 space-y-2.5 shadow-lg">
            <a href="#fitur-utama" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-1.5 hover:text-blue-600">Fitur Unggulan</a>
            <a href="#strava-running" @click="mobileMenu = false" class="flex items-center justify-between text-sm font-semibold text-blue-600 py-1.5">
                <span>Lari GPS (Strava Mode)</span>
                <span class="px-2 py-0.5 text-[10px] bg-blue-100 text-blue-700 rounded-md font-bold">Baru</span>
            </a>
            <a href="#gym-logger" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-1.5 hover:text-blue-600">Gym & Angkat Beban</a>
            <a href="#muscle-heatmap" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-1.5 hover:text-blue-600">Muscle Heatmap</a>
            <a href="#pwa-offline" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-1.5 hover:text-blue-600">Offline PWA</a>
            <a href="#faq" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-1.5 hover:text-blue-600">FAQ</a>
            @guest
                <div class="pt-3 border-t border-slate-100 flex gap-2">
                    <a href="{{ route('demo.login') }}" class="flex-1 py-2.5 rounded-xl bg-slate-100 text-center text-xs font-bold text-slate-700 hover:bg-slate-200">Demo 1-Klik</a>
                    <a href="{{ route('register') }}" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-center text-xs font-bold text-white shadow-xs">Daftar Akun</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- 2. HERO SECTION -->
    <section class="relative pt-10 sm:pt-16 pb-16 sm:pb-24 overflow-hidden">
        <!-- Soft Ambient Background Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-blue-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-1/3 right-10 w-[300px] h-[300px] bg-indigo-400/10 rounded-full blur-2xl pointer-events-none -z-10"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Aplikasi Fitness & Lari All-in-One Tanpa Iklan</span>
            </div>

            <!-- Big Main Headline -->
            <h1 class="font-display font-black text-3xl sm:text-5xl md:text-6xl text-slate-900 tracking-tight leading-[1.15]">
                Catat Angkatan di Gym.<br class="hidden sm:inline">
                Lacak Rute & Pace Lari GPS.<br>
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Semua dalam Satu Aplikasi.</span>
            </h1>

            <!-- Subheading -->
            <p class="text-sm sm:text-base md:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                GerakIn menggabungkan kemudahan pencatatan set & beban gym dengan pelacak lari real-time ala Strava. Dilengkapi muscle heatmap interaktif, timer istirahat otomatis, dan kemampuan offline penuh.
            </p>

            <!-- Dual Action CTAs -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-sm sm:text-base shadow-lg shadow-blue-500/25 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span>Mulai Sekarang (Gratis)</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>

                <a href="{{ route('demo.login') }}" class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-display font-bold text-sm sm:text-base border border-slate-200 shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span>Coba Demo 1-Klik</span>
                </a>
            </div>

            <!-- Trust Badges -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-600">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>100% Bebas Iklan</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Jalan Lancar di Basement (Offline PWA)</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Peta GPS & Pecahan Pace KM</span>
                </span>
            </div>
        </div>

        <!-- 3. INTERACTIVE LIVE APPLICATION SHOWCASE -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 mt-12 sm:mt-16" x-data="{ activeTab: 'running' }">
            <div class="bg-white rounded-3xl sm:rounded-[32px] p-3 sm:p-5 border border-slate-200 shadow-xl shadow-slate-200/50">
                <!-- Tab Controls -->
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 p-1.5 bg-slate-100 rounded-2xl mb-4 overflow-x-auto no-scrollbar">
                    <button type="button"
                            @click="activeTab = 'running'"
                            class="px-3.5 sm:px-5 py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'running' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>1. Pelacak Lari GPS (Strava)</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'gym'"
                            class="px-3.5 sm:px-5 py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'gym' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>2. Pencatat Gym & Timer</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'heatmap'"
                            class="px-3.5 sm:px-5 py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'heatmap' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>3. Muscle Heatmap</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'analytics'"
                            class="px-3.5 sm:px-5 py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'analytics' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>4. Analitik & Streak</span>
                    </button>
                </div>

                <!-- Showcase Canvas Frame -->
                <div class="bg-slate-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 min-h-[380px] sm:min-h-[460px] flex items-center justify-center">

                    <!-- TAB 1: RUNNING TRACKER PREVIEW -->
                    <div x-show="activeTab === 'running'" class="w-full max-w-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                <span class="text-xs font-bold text-slate-800">Sesi Lari Real-Time: Gelora Bung Karno</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black">GPS Akurat</span>
                        </div>

                        <!-- Mini Simulated Running Map Mockup -->
                        <div class="bg-slate-800 rounded-2xl h-48 sm:h-60 relative overflow-hidden flex items-center justify-center border border-slate-700 shadow-inner">
                            <!-- SVG Stylized Route Map Graphics -->
                            <svg class="absolute inset-0 w-full h-full opacity-60" viewBox="0 0 600 240" fill="none">
                                <path d="M50 180 C 120 180, 160 80, 260 80 C 360 80, 390 190, 480 160 C 530 140, 560 60, 580 40" stroke="#3B82F6" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="8 8" class="animate-pulse"/>
                                <circle cx="50" cy="180" r="10" fill="#10B981" stroke="#FFFFFF" stroke-width="3"/>
                                <circle cx="480" cy="160" r="8" fill="#3B82F6" stroke="#FFFFFF" stroke-width="2.5"/>
                            </svg>
                            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Peta Voyager OpenStreetMap</span>
                            </div>
                            <div class="absolute bottom-3 right-3 bg-slate-900/90 text-white px-3 py-1.5 rounded-xl text-xs font-mono font-bold">
                                GPS Tracking: Live Polyline
                            </div>
                        </div>

                        <!-- Live Telemetry HUD -->
                        <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center">
                            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-600 uppercase">Jarak Tempuh</div>
                                <div class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-0.5">5.24 <span class="text-xs text-blue-600">km</span></div>
                            </div>
                            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-600 uppercase">Waktu Gerak</div>
                                <div class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-0.5">28:15</div>
                            </div>
                            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-600 uppercase">Pace Saat Ini</div>
                                <div class="font-display font-black text-xl sm:text-2xl text-blue-600 mt-0.5">5'23" <span class="text-[10px] text-slate-600 font-semibold">/km</span></div>
                            </div>
                            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-600 uppercase">Kalori Terbakar</div>
                                <div class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-0.5">385 <span class="text-xs text-slate-600">kcal</span></div>
                            </div>
                        </div>

                        <!-- Splits Preview Snippet -->
                        <div class="bg-white p-3 rounded-2xl border border-slate-200 flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700">Pecahan KM 5 (Split Tercepat):</span>
                            <span class="font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">5'10" /km • 👑 Rekor Lap Hari Ini</span>
                        </div>
                    </div>

                    <!-- TAB 2: GYM WORKOUT LOGGER PREVIEW -->
                    <div x-show="activeTab === 'gym'" x-cloak class="w-full max-w-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-display font-black text-base text-slate-900">Sesi Latihan: Push Day Hypertrophy</h4>
                                <p class="text-xs text-slate-600">Smart Progressive Overload dengan Riwayat Sesi Terakhir</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black">Rest: 01:15</span>
                        </div>

                        <!-- Exercise Card Mockup -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs">1</div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-900">Barbell Bench Press</div>
                                        <div class="text-[11px] text-slate-600">Otot Utama: Dada (Chest) • Alat: Barbell</div>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">
                                    + Rekor Baru (PR)
                                </span>
                            </div>

                            <!-- Sets Table Mockup -->
                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 text-slate-600 font-bold text-[11px]">
                                    <span class="w-12">SET</span>
                                    <span class="w-24 text-center">SEBELUMNYA</span>
                                    <span class="w-20 text-center">BEBAN (KG)</span>
                                    <span class="w-16 text-center">REPS</span>
                                    <span class="w-10 text-right">STATUS</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50/60 border border-emerald-200 text-slate-900 font-semibold">
                                    <span class="w-12 font-bold text-emerald-800">Set 1</span>
                                    <span class="w-24 text-center text-slate-600">75 kg × 10</span>
                                    <span class="w-20 text-center font-bold">80 kg</span>
                                    <span class="w-16 text-center font-bold">10 reps</span>
                                    <span class="w-10 text-right text-emerald-600 font-black">✓ Selesai</span>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-blue-50/60 border border-blue-200 text-slate-900 font-semibold">
                                    <span class="w-12 font-bold text-blue-800">Set 2</span>
                                    <span class="w-24 text-center text-slate-600">75 kg × 8</span>
                                    <span class="w-20 text-center font-bold">80 kg</span>
                                    <span class="w-16 text-center font-bold">8 reps</span>
                                    <span class="w-10 text-right text-blue-600 font-black">⏱ 00:45</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                <span class="text-slate-600">Total Angkatan: </span>
                                <strong class="text-slate-900 font-black">3,450 kg</strong>
                            </div>
                            <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                <span class="text-slate-600">Durasi Sesi: </span>
                                <strong class="text-slate-900 font-black">42 menit</strong>
                            </div>
                            <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                <span class="text-slate-600">Rest Timer: </span>
                                <strong class="text-blue-600 font-black">Auto Sound/Vibrate</strong>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: MUSCLE HEATMAP PREVIEW -->
                    <div x-show="activeTab === 'heatmap'" x-cloak class="w-full max-w-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-display font-black text-base text-slate-900">Peta Otot Interaktif (Muscle Heatmap)</h4>
                                <p class="text-xs text-slate-600">Visualisasi anatomis bagian tubuh yang terlatih dalam 7 hari terakhir</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">Volume Tinggi: Dada & Punggung</span>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-center justify-around gap-6">
                            <!-- Visual Anatomy Representation Cards -->
                            <div class="space-y-2 text-center">
                                <div class="w-36 h-48 rounded-2xl bg-slate-900 p-3 flex flex-col justify-between text-white relative overflow-hidden shadow-md">
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Tampak Depan</div>
                                    <div class="space-y-1">
                                        <div class="px-2 py-1 bg-amber-500 rounded-md text-[10px] font-bold">Dada: 12 Set (Tinggi)</div>
                                        <div class="px-2 py-1 bg-blue-600 rounded-md text-[10px] font-bold">Bahu: 8 Set</div>
                                        <div class="px-2 py-1 bg-emerald-600 rounded-md text-[10px] font-bold">Kaki: 6 Set</div>
                                    </div>
                                    <div class="text-[9px] text-slate-400">Anatomi Anterior</div>
                                </div>
                            </div>

                            <div class="space-y-2 text-center">
                                <div class="w-36 h-48 rounded-2xl bg-slate-900 p-3 flex flex-col justify-between text-white relative overflow-hidden shadow-md">
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Tampak Belakang</div>
                                    <div class="space-y-1">
                                        <div class="px-2 py-1 bg-amber-500 rounded-md text-[10px] font-bold">Punggung: 14 Set</div>
                                        <div class="px-2 py-1 bg-blue-600 rounded-md text-[10px] font-bold">Triceps: 8 Set</div>
                                        <div class="px-2 py-1 bg-slate-700 rounded-md text-[10px] font-bold">Hamstring: Istirahat</div>
                                    </div>
                                    <div class="text-[9px] text-slate-400">Anatomi Posterior</div>
                                </div>
                            </div>

                            <div class="text-left space-y-2 max-w-xs text-xs">
                                <div class="font-bold text-slate-900 text-sm">Bagaimana Heatmap Bekerja?</div>
                                <p class="text-slate-600">Setiap kali Anda mencatat set latihan, algoritma GerakIn menghitung intensitas otot secara real-time. Otot yang membutuhkan pemulihan akan ditandai agar tidak terjadi overtraining.</p>
                                <div class="pt-1 flex items-center gap-2 text-[11px] font-semibold text-blue-600">
                                    <span>Tersedia untuk siluet pria dan wanita</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: ANALYTICS & PROGRESS -->
                    <div x-show="activeTab === 'analytics'" x-cloak class="w-full max-w-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-display font-black text-base text-slate-900">Grafik Konsistensi & Riwayat Beban</h4>
                                <p class="text-xs text-slate-600">Pantau perkembangan One Rep Max (1RM) dan streak mingguan</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">🔥 6 Minggu Streak</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                <div class="text-[11px] font-bold uppercase text-slate-600">Target Mingguan</div>
                                <div class="font-display font-black text-3xl text-slate-900 mt-1">4 / 4</div>
                                <div class="text-[11px] text-emerald-600 font-bold mt-1">✓ Target Terpenuhi</div>
                            </div>

                            <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                <div class="text-[11px] font-bold uppercase text-slate-600">Volume Angkatan</div>
                                <div class="font-display font-black text-3xl text-blue-600 mt-1">48.2 <span class="text-sm">ton</span></div>
                                <div class="text-[11px] text-slate-600 mt-1">+12% dari bulan lalu</div>
                            </div>

                            <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                <div class="text-[11px] font-bold uppercase text-slate-600">Total Sesi Lari</div>
                                <div class="font-display font-black text-3xl text-slate-900 mt-1">18 <span class="text-sm font-semibold">kali</span></div>
                                <div class="text-[11px] text-blue-600 font-bold mt-1">84.5 km tercatat</div>
                            </div>
                        </div>

                        <!-- Progress Bar Simulation -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-700">Progres Beban Bench Press (Target: 100 kg)</span>
                                <span class="text-blue-600">85 kg (85%)</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- 4. CORE FEATURES GRID -->
    <section id="fitur-utama" class="py-16 sm:py-24 bg-white border-y border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                    Dirancang untuk Olahragawan
                </span>
                <h2 class="font-display font-black text-2xl sm:text-4xl text-slate-900">
                    Semua yang Anda Butuhkan untuk Mencapai Tubuh Impian
                </h2>
                <p class="text-sm sm:text-base text-slate-600">
                    Dibangun tanpa fitur mubazir. Fokus pada kemudahan mencatat data saat Anda sedang berkeringat.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Feature 1: Strava Running Tracker -->
                <div id="strava-running" class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Pelacak Lari GPS ala Strava</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Lacak lintasan lari real-time di atas peta interaktif. Dilengkapi kalkulator kecepatan (min/km), pecahan per kilometer (lap splits), kalori, dan mode simulasi demo.
                    </p>
                </div>

                <!-- Feature 2: Smart Gym Logger -->
                <div id="gym-logger" class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Smart Workout Logger</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Mengingat beban dan repetisi dari sesi sebelumnya secara otomatis. Ketahui kapan saatnya menaikkan beban (*progressive overload*) tanpa perlu membuka buku catatan.
                    </p>
                </div>

                <!-- Feature 3: Auto Rest Timer -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Rest Timer Otomatis</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Timer hitung mundur menyala otomatis begitu Anda mencentang set selesai. Disertai notifikasi suara dan getar haptic saat waktu istirahat habis.
                    </p>
                </div>

                <!-- Feature 4: Interactive Muscle Heatmap -->
                <div id="muscle-heatmap" class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Muscle Heatmap Anatomi</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Lihat visual tubuh yang menyala berdasarkan volume latihan mingguan Anda. Memastikan keseimbangan latihan antara tubuh bagian atas dan bawah.
                    </p>
                </div>

                <!-- Feature 5: 100+ Exercise Library -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">100+ Database Gerakan & Kustom</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Database lengkap gerakan Barbell, Dumbbell, Mesin, hingga Kalistenik tubuh. Bebas menambahkan gerakan kustom Anda sendiri kapan saja.
                    </p>
                </div>

                <!-- Feature 6: Offline PWA & Zero Ads -->
                <div id="pwa-offline" class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Offline First & PWA Installable</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pasang di homescreen iOS/Android layaknya aplikasi native. Tetap mencatat dengan lancar saat Anda berlatih di basement gym tanpa internet.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. HOW IT WORKS / USER WALKTHROUGH -->
    <section class="py-16 sm:py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-12">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Langkah Sederhana</span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900">
                    Mulai dalam Hitungan Detik
                </h2>
                <p class="text-xs sm:text-sm text-slate-600">Tanpa formulir rumit atau pengaturan bertele-tele.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-display font-black text-base flex items-center justify-center mb-4">
                        1
                    </div>
                    <h3 class="font-display font-bold text-base text-slate-900 mb-2">Pilih Jenis Latihan</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tekan tombol Mulai di aplikasi, lalu tentukan apakah Anda ingin sesi gym (angkat beban) atau mulai lari luar ruangan dengan GPS.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-display font-black text-base flex items-center justify-center mb-4">
                        2
                    </div>
                    <h3 class="font-display font-bold text-base text-slate-900 mb-2">Rekam & Istirahat</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Centang set angkatan Anda untuk menyalakan timer istirahat otomatis, atau biarkan pelacak GPS merekam rute dan tempo lari per kilometer Anda.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-display font-black text-base flex items-center justify-center mb-4">
                        3
                    </div>
                    <h3 class="font-display font-bold text-base text-slate-900 mb-2">Simpan & Rayakan Progres</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Selesaikan sesi untuk mendapatkan kartu rekap, visualisasi rute peta, notifikasi pemecahan rekor (PR), serta perkembangan heatmap otot mingguan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. COMPARISON TABLE -->
    <section class="py-16 sm:py-20 bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Perbandingan Jelas</span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-white">
                    Mengapa Harus GerakIn?
                </h2>
                <p class="text-xs sm:text-sm text-slate-400">Lihat perbandingan langsung dengan aplikasi pencatat olahraga konvensional.</p>
            </div>

            <div class="bg-slate-800/80 rounded-3xl border border-slate-700 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-700 bg-slate-800">
                                <th class="p-4 sm:p-5 font-bold text-slate-300">Fitur & Kemampuan</th>
                                <th class="p-4 sm:p-5 font-display font-black text-blue-400 text-sm sm:text-base">GerakIn</th>
                                <th class="p-4 sm:p-5 font-semibold text-slate-400">Aplikasi Lain</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/60">
                            <tr>
                                <td class="p-4 sm:p-5 text-slate-300">Kombinasi Lari GPS + Catatan Gym</td>
                                <td class="p-4 sm:p-5 font-bold text-emerald-400">✓ Lengkap Terintegrasi</td>
                                <td class="p-4 sm:p-5 text-slate-400">✕ Harus pakai 2 aplikasi terpisah</td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-slate-300">Biaya Langganan & Iklan</td>
                                <td class="p-4 sm:p-5 font-bold text-emerald-400">✓ 100% Gratis & Nol Iklan</td>
                                <td class="p-4 sm:p-5 text-slate-400">✕ Iklan mengganggu & langganan mahal</td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-slate-300">Kemampuan Mode Offline (Basement Gym)</td>
                                <td class="p-4 sm:p-5 font-bold text-emerald-400">✓ Berfungsi Penuh (PWA)</td>
                                <td class="p-4 sm:p-5 text-slate-400">✕ Loading terus tanpa internet</td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-slate-300">Muscle Heatmap Anatomi Otomatis</td>
                                <td class="p-4 sm:p-5 font-bold text-emerald-400">✓ Ada (Depan & Belakang)</td>
                                <td class="p-4 sm:p-5 text-slate-400">✕ Fitur terkunci paywall</td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 text-slate-300">Pecahan Waktu Per Kilometer (KM Splits)</td>
                                <td class="p-4 sm:p-5 font-bold text-emerald-400">✓ Lengkap dengan Audio Chime</td>
                                <td class="p-4 sm:p-5 text-slate-400">✕ Terbatas / berbayar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FAQ ACCORDION -->
    <section id="faq" class="py-16 sm:py-24 bg-white" x-data="{ openFaq: null }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-8">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Tanya Jawab</span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900">
                    Pertanyaan yang Sering Diajukan
                </h2>
            </div>

            <div class="space-y-3">
                <!-- FAQ 1 -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <button type="button" @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full p-4 sm:p-5 text-left font-bold text-sm sm:text-base text-slate-900 flex justify-between items-center bg-slate-50/50 hover:bg-slate-50">
                        <span>Apakah aplikasi GerakIn ini gratis?</span>
                        <svg class="w-4 h-4 transition-transform text-slate-600" :class="openFaq === 1 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="p-4 sm:p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                        Ya, GerakIn 100% gratis dan bebas iklan selamanya. Semua fitur, mulai dari pencatatan angkat beban, pelacak lari GPS, hingga muscle heatmap dapat digunakan tanpa biaya langganan apapun.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <button type="button" @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full p-4 sm:p-5 text-left font-bold text-sm sm:text-base text-slate-900 flex justify-between items-center bg-slate-50/50 hover:bg-slate-50">
                        <span>Bagaimana cara menginstal GerakIn di HP (iPhone & Android)?</span>
                        <svg class="w-4 h-4 transition-transform text-slate-600" :class="openFaq === 2 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="p-4 sm:p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                        GerakIn menggunakan teknologi PWA (Progressive Web App). Cukup buka situs ini di browser HP Anda:<br>
                        • <strong>iPhone (Safari):</strong> Ketuk tombol Bagikan (Share) lalu pilih <em>"Tambahkan ke Layar Utama" (Add to Home Screen)</em>.<br>
                        • <strong>Android (Chrome):</strong> Ketuk ikon titik tiga lalu pilih <em>"Pasang Aplikasi" (Install App)</em>.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <button type="button" @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full p-4 sm:p-5 text-left font-bold text-sm sm:text-base text-slate-900 flex justify-between items-center bg-slate-50/50 hover:bg-slate-50">
                        <span>Apakah bisa digunakan saat lari di treadmill atau gym tanpa GPS?</span>
                        <svg class="w-4 h-4 transition-transform text-slate-600" :class="openFaq === 3 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="p-4 sm:p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                        Bisa! Untuk latihan dalam ruangan atau saat sinyal GPS tidak tersedia, Anda bisa menggunakan fitur pencatatan durasi manual atau menyalakan <strong>Mode Simulasi Demo</strong> yang telah disediakan untuk melihat simulasi visual rute dan penghitungan pace.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <button type="button" @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full p-4 sm:p-5 text-left font-bold text-sm sm:text-base text-slate-900 flex justify-between items-center bg-slate-50/50 hover:bg-slate-50">
                        <span>Apakah data riwayat latihan saya aman?</span>
                        <svg class="w-4 h-4 transition-transform text-slate-600" :class="openFaq === 4 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="p-4 sm:p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                        Sangat aman. Data Anda tersimpan di database terenkripsi dan Anda memiliki kendali penuh untuk mengekspor seluruh riwayat latihan dalam format JSON melalui menu Profil kapan saja.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FINAL CALL TO ACTION -->
    <section class="py-16 sm:py-24 bg-gradient-to-b from-white to-blue-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center space-y-6">
            <h2 class="font-display font-black text-3xl sm:text-4xl md:text-5xl text-slate-900 tracking-tight">
                Mulai Bergerak dan Jadilah Versi Terbaik Diri Anda Hari Ini.
            </h2>
            <p class="text-sm sm:text-base text-slate-600 max-w-xl mx-auto">
                Bergabunglah dengan pelari dan lifter yang telah beralih ke GerakIn untuk mencatat latihan secara konsisten dan tanpa distraksi.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-sm sm:text-base shadow-lg shadow-blue-500/25 active:scale-95 transition-all">
                    Daftar Akun Gratis Sekarang
                </a>
                <a href="{{ route('demo.login') }}" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 font-display font-bold text-sm sm:text-base border border-slate-300 shadow-sm transition-all">
                    Masuk Akun Demo (1-Klik)
                </a>
            </div>
        </div>
    </section>

    <!-- 9. FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-10 text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="/icons/icon-72x72.png" alt="GerakIn" class="w-6 h-6 rounded-lg">
                <span class="font-display font-extrabold text-sm text-slate-900">GerakIn</span>
                <span>• Workout & Running Habit Tracker</span>
            </div>
            <div class="flex items-center gap-4 font-semibold text-slate-600">
                <a href="{{ route('login') }}" class="hover:text-blue-600">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-blue-600">Daftar</a>
                <a href="{{ route('demo.login') }}" class="hover:text-blue-600">Demo</a>
                <a href="#faq" class="hover:text-blue-600">Bantuan</a>
            </div>
            <div>
                &copy; {{ date('Y') }} GerakIn. Dibuat untuk mereka yang tak pernah berhenti bergerak.
            </div>
        </div>
    </footer>

</body>
</html>
