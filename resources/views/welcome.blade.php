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
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-600 selection:text-white"
      x-data="{
          mobileMenu: false,
          pwaModalOpen: false,
          apkModalOpen: false,
          apkProgress: 0,
          apkStage: 'idle',
          apkStatusText: 'Menyiapkan berkas unduhan...',
          apkDownloadTimer: null,
          startApkDownload() {
              this.apkModalOpen = true;
              this.apkStage = 'preparing';
              this.apkProgress = 15;
              this.apkStatusText = 'Menghubungkan ke server GerakIn...';
              
              if (this.apkDownloadTimer) clearInterval(this.apkDownloadTimer);
              
              this.apkDownloadTimer = setInterval(() => {
                  if (this.apkProgress < 40) {
                      this.apkProgress += 12;
                      this.apkStatusText = 'Memeriksa paket aplikasi v1.0.0 (43 MB)...';
                  } else if (this.apkProgress < 75) {
                      this.apkProgress += 15;
                      this.apkStatusText = 'Menyiapkan berkas APK resmi GerakIn...';
                  } else if (this.apkProgress < 95) {
                      this.apkProgress += 10;
                      this.apkStatusText = 'Memulai proses transfer ke perangkat Anda...';
                  } else {
                      clearInterval(this.apkDownloadTimer);
                      this.apkProgress = 100;
                      this.apkStage = 'completed';
                      this.apkStatusText = 'Unduhan berhasil dimulai!';
                      this.triggerFileDownload();
                  }
              }, 250);
          },
          triggerFileDownload() {
              const link = document.createElement('a');
              link.href = '/downloads/gerakin-app.apk';
              link.download = 'gerakin-v1.0.0.apk';
              document.body.appendChild(link);
              link.click();
              setTimeout(() => {
                  if (document.body.contains(link)) document.body.removeChild(link);
              }, 1200);
          }
      }"
      x-init="window.addEventListener('pwa-installable', () => {})">

    <!-- 1. TOP NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2 sm:gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-2.5 shrink-0 group">
                <img src="/icons/icon-72x72.png" alt="GerakIn Logo" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl shadow-xs group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="font-display font-extrabold text-lg sm:text-2xl tracking-tight text-slate-900 flex items-center">
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

            <!-- Auth CTAs (Responsive: Clean on Mobile, Full on Desktop) -->
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                <!-- PWA Install Button (Desktop & Tablet) -->
                <button type="button"
                        @click="window.triggerPwaInstall ? window.triggerPwaInstall() : (pwaModalOpen = true)"
                        class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-colors border border-blue-200 whitespace-nowrap"
                        title="Pasang Aplikasi GerakIn">
                    <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Pasang App</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="whitespace-nowrap px-3.5 py-1.5 sm:px-5 sm:py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all flex items-center gap-1.5">
                        <span>Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <!-- Demo 1-Klik (Desktop & Tablet) -->
                    <a href="{{ route('demo.login') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors border border-slate-200 whitespace-nowrap" title="Coba langsung tanpa registrasi">
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <span>Demo 1-Klik</span>
                    </a>

                    <!-- Masuk (Hidden on small phones to prevent overflow, accessible in hamburger) -->
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex whitespace-nowrap px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 transition-colors">
                        Masuk
                    </a>

                    <!-- Daftar Button -->
                    <a href="{{ route('register') }}" class="whitespace-nowrap px-3 py-1.5 sm:px-5 sm:py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 active:scale-95 transition-all">
                        Daftar
                    </a>
                @endauth

                <!-- Mobile Menu Button (Always fully visible, never cut off) -->
                <button type="button" @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors shrink-0" aria-label="Menu Navigasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Nav (Clean, Scrollable, Complete) -->
        <div x-show="mobileMenu"
             x-cloak
             @click.away="mobileMenu = false"
             class="lg:hidden bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 py-4 space-y-3 shadow-xl max-h-[calc(100vh-4.5rem)] overflow-y-auto">
            <!-- Mobile APK Install Highlight Button -->
            <button type="button" @click="mobileMenu = false; startApkDownload()" class="w-full flex items-center justify-between text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-3.5 py-2.5 rounded-xl border border-emerald-200 transition-colors">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.552 0 .9993.4486.9993 1.0003s-.4473 1.0003-.9993 1.0003m-11.046 0c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.5511 0 .9993.4486.9993 1.0003s-.4482 1.0003-.9993 1.0003m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.125 12 8.125s-3.5902.2866-5.1368.8248L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                    </svg>
                    <span>Unduh APK Android (v1.0)</span>
                </div>
                <span class="text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded-md font-extrabold uppercase">Unduh</span>
            </button>

            <!-- Mobile PWA Install Highlight Button -->
            <button type="button" @click="mobileMenu = false; (window.triggerPwaInstall ? window.triggerPwaInstall() : (pwaModalOpen = true))" class="w-full flex items-center justify-between text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-3.5 py-2.5 rounded-xl border border-blue-200 transition-colors">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Pasang Aplikasi di HP (PWA)</span>
                </div>
                <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-md font-extrabold uppercase">Install</span>
            </button>

            <!-- Navigation Links -->
            <div class="space-y-1 pt-1">
                <a href="#fitur-utama" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-2 px-2 rounded-xl hover:bg-slate-100 transition-colors">Fitur Unggulan</a>
                <a href="#strava-running" @click="mobileMenu = false" class="flex items-center justify-between text-sm font-semibold text-blue-600 py-2 px-2 rounded-xl hover:bg-blue-50 transition-colors">
                    <span>Lari GPS (Strava Mode)</span>
                    <span class="px-2 py-0.5 text-[10px] bg-blue-100 text-blue-700 rounded-md font-bold">Baru</span>
                </a>
                <a href="#gym-logger" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-2 px-2 rounded-xl hover:bg-slate-100 transition-colors">Gym & Angkat Beban</a>
                <a href="#muscle-heatmap" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-2 px-2 rounded-xl hover:bg-slate-100 transition-colors">Muscle Heatmap</a>
                <a href="#pwa-offline" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-2 px-2 rounded-xl hover:bg-slate-100 transition-colors">Offline PWA</a>
                <a href="#faq" @click="mobileMenu = false" class="block text-sm font-semibold text-slate-700 py-2 px-2 rounded-xl hover:bg-slate-100 transition-colors">FAQ</a>
            </div>

            @guest
                <!-- Guest Actions in Mobile Menu -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <a href="{{ route('demo.login') }}" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-center text-xs font-bold text-slate-800 flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <span>Coba Demo 1-Klik Langsung</span>
                    </a>
                    <div class="flex gap-2">
                        <a href="{{ route('login') }}" class="flex-1 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-center text-xs font-bold text-slate-700 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-center text-xs font-bold text-white shadow-xs transition-colors">
                            Daftar Gratis
                        </a>
                    </div>
                </div>
            @else
                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ route('dashboard') }}" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-center text-xs font-bold text-white shadow-xs flex items-center justify-center gap-1.5">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
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

            <!-- Action CTAs with APK & PWA Install (Clean Responsive 2-tier layout on mobile) -->
            <div class="flex flex-col items-center justify-center gap-2.5 sm:gap-3 pt-2 w-full max-w-xl mx-auto">
                <!-- Tier 1: Primary Actions -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 w-full">
                    <a href="{{ route('register') }}" class="w-full px-6 py-3.5 sm:py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-sm sm:text-base shadow-lg shadow-blue-500/25 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span>Mulai Sekarang (Gratis)</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>

                    <button type="button"
                            @click="startApkDownload()"
                            class="w-full px-5 py-3.5 sm:py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-display font-bold text-sm sm:text-base shadow-md shadow-emerald-600/20 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer"
                            title="Unduh File APK Android Langsung">
                        <svg class="w-5 h-5 text-white shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.552 0 .9993.4486.9993 1.0003s-.4473 1.0003-.9993 1.0003m-11.046 0c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.5511 0 .9993.4486.9993 1.0003s-.4482 1.0003-.9993 1.0003m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.125 12 8.125s-3.5902.2866-5.1368.8248L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                        </svg>
                        <span>Unduh APK (Android)</span>
                    </button>
                </div>

                <!-- Tier 2: Secondary Options (Side-by-side on mobile) -->
                <div class="grid grid-cols-2 gap-2 sm:gap-3 w-full">
                    <a href="{{ route('demo.login') }}" class="w-full px-3 sm:px-5 py-3 sm:py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-display font-bold text-xs sm:text-sm border border-slate-200 shadow-xs active:scale-95 transition-all flex items-center justify-center gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <span>Demo 1-Klik</span>
                    </a>

                    <button type="button"
                            @click="window.triggerPwaInstall ? window.triggerPwaInstall() : (pwaModalOpen = true)"
                            class="w-full px-3 sm:px-5 py-3 sm:py-3.5 rounded-2xl bg-blue-50/80 hover:bg-blue-100/80 text-blue-800 font-display font-bold text-xs sm:text-sm border border-blue-200 shadow-xs active:scale-95 transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Pasang PWA</span>
                    </button>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="pt-3 sm:pt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 text-xs font-semibold text-slate-600">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>100% Bebas Iklan</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Offline PWA (Basement Friendly)</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Peta GPS & Pecahan Lap KM</span>
                </span>
            </div>
        </div>

        <!-- 3. INTERACTIVE LIVE APPLICATION SHOWCASE -->
        <div class="max-w-5xl mx-auto px-3 sm:px-6 mt-10 sm:mt-16" x-data="{ activeTab: 'running' }">
            <div class="bg-white rounded-3xl p-2.5 sm:p-5 border border-slate-200 shadow-xl shadow-slate-200/50">
                
                <!-- Tab Controls (Responsive, Thumb-friendly, No horizontal clipping) -->
                <div class="flex items-center justify-start sm:justify-center gap-1 sm:gap-2 p-1 sm:p-1.5 bg-slate-100 rounded-2xl mb-3 sm:mb-4 overflow-x-auto no-scrollbar scroll-smooth">
                    <button type="button"
                            @click="activeTab = 'running'"
                            class="shrink-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-1.5 sm:gap-2 whitespace-nowrap cursor-pointer"
                            :class="activeTab === 'running' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Lari GPS</span>
                        <span class="hidden sm:inline">(Strava Mode)</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'gym'"
                            class="shrink-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-1.5 sm:gap-2 whitespace-nowrap cursor-pointer"
                            :class="activeTab === 'gym' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Pencatat Gym</span>
                        <span class="hidden sm:inline">& Timer</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'heatmap'"
                            class="shrink-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-1.5 sm:gap-2 whitespace-nowrap cursor-pointer"
                            :class="activeTab === 'heatmap' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Muscle Heatmap</span>
                    </button>

                    <button type="button"
                            @click="activeTab = 'analytics'"
                            class="shrink-0 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all flex items-center gap-1.5 sm:gap-2 whitespace-nowrap cursor-pointer"
                            :class="activeTab === 'analytics' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Analitik</span>
                        <span class="hidden sm:inline">& Streak</span>
                    </button>
                </div>

                <!-- Showcase Canvas Frame (Tighter padding on mobile to maximize content area) -->
                <div class="bg-slate-50 rounded-2xl sm:rounded-3xl p-3 sm:p-6 border border-slate-200/80 min-h-[350px] sm:min-h-[460px] flex items-center justify-center">

                    <!-- TAB 1: RUNNING TRACKER PREVIEW -->
                    <div x-show="activeTab === 'running'" class="w-full max-w-3xl space-y-3 sm:space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 truncate">Sesi Lari: Gelora Bung Karno</span>
                            </div>
                            <span class="shrink-0 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] sm:text-xs font-black">GPS Akurat</span>
                        </div>

                        <!-- Simulated Running Map Mockup (No Voyager/OSM text, ultra-sleek dark runner HUD) -->
                        <div class="bg-[#0f172a] rounded-2xl h-44 sm:h-60 relative overflow-hidden flex items-center justify-center border border-slate-800 shadow-inner">
                            <!-- Subtle City Grid Lines -->
                            <div class="absolute inset-0 opacity-15" style="background-image: linear-gradient(#334155 1px, transparent 1px), linear-gradient(90deg, #334155 1px, transparent 1px); background-size: 32px 32px;"></div>

                            <!-- SVG Stylized Route Map Graphics -->
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 600 240" fill="none" preserveAspectRatio="none">
                                <!-- Base Route Shadow Glow -->
                                <path d="M50 180 C 130 180, 160 70, 260 70 C 360 70, 390 190, 480 150 C 520 130, 550 70, 580 40" stroke="#0284c7" stroke-width="10" stroke-linecap="round" opacity="0.25"/>
                                <!-- Active Running Polyline -->
                                <path d="M50 180 C 130 180, 160 70, 260 70 C 360 70, 390 190, 480 150 C 520 130, 550 70, 580 40" stroke="#38bdf8" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="10 8" class="animate-pulse"/>
                                
                                <!-- Start Marker (KM 0) -->
                                <circle cx="50" cy="180" r="10" fill="#10B981" stroke="#FFFFFF" stroke-width="3"/>
                                <text x="50" y="210" fill="#10B981" font-size="12" font-weight="bold" text-anchor="middle">START</text>

                                <!-- KM Waypoints -->
                                <circle cx="200" cy="115" r="4" fill="#94A3B8"/>
                                <circle cx="330" cy="115" r="4" fill="#94A3B8"/>
                                <circle cx="430" cy="175" r="4" fill="#94A3B8"/>

                                <!-- Current Runner Position Marker with Pulsing Ring -->
                                <circle cx="480" cy="150" r="14" fill="#38bdf8" opacity="0.3" class="animate-ping"/>
                                <circle cx="480" cy="150" r="8" fill="#0284c7" stroke="#FFFFFF" stroke-width="3"/>
                            </svg>

                            <!-- Clean Authentic Route Badge (Replaces Voyager OpenStreetMap) -->
                            <div class="absolute top-2.5 sm:top-3 left-2.5 sm:left-3 bg-slate-900/85 backdrop-blur-md px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-700/80 text-[11px] sm:text-xs font-bold text-white shadow-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>Rute GBK Senayan (Loop 5K)</span>
                            </div>

                            <!-- Telemetry Status Tag -->
                            <div class="absolute bottom-2.5 sm:bottom-3 right-2.5 sm:right-3 bg-slate-900/85 backdrop-blur-md text-emerald-400 border border-slate-700/80 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-[10px] sm:text-xs font-mono font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Live GPS • Elevasi +18m</span>
                            </div>
                        </div>

                        <!-- Live Telemetry HUD (2 columns on mobile, 4 on desktop — No text wrapping or collision) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 text-center">
                            <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Jarak Tempuh</div>
                                <div class="font-display font-black text-lg sm:text-2xl text-slate-900 mt-0.5">5.24 <span class="text-xs text-blue-600 font-bold">km</span></div>
                            </div>
                            <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Waktu Gerak</div>
                                <div class="font-display font-black text-lg sm:text-2xl text-slate-900 mt-0.5">28:15</div>
                            </div>
                            <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pace Saat Ini</div>
                                <div class="font-display font-black text-lg sm:text-2xl text-blue-600 mt-0.5">5'23" <span class="text-[10px] text-slate-500 font-semibold">/km</span></div>
                            </div>
                            <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kalori Terbakar</div>
                                <div class="font-display font-black text-lg sm:text-2xl text-slate-900 mt-0.5">385 <span class="text-xs text-slate-500 font-semibold">kcal</span></div>
                            </div>
                        </div>

                        <!-- Splits Preview Snippet (Flex-col on mobile, flex-row on desktop) -->
                        <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1.5 sm:gap-2 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="font-bold text-slate-700">Pecahan KM 5 (Split Tercepat):</span>
                            </div>
                            <span class="font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-[11px] sm:text-xs">
                                5'10" /km • Rekor Lap Hari Ini
                            </span>
                        </div>
                    </div>

                    <!-- TAB 2: GYM WORKOUT LOGGER PREVIEW -->
                    <div x-show="activeTab === 'gym'" x-cloak class="w-full max-w-3xl space-y-3 sm:space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1 sm:gap-2">
                            <div>
                                <h4 class="font-display font-black text-sm sm:text-base text-slate-900">Sesi Latihan: Push Day Hypertrophy</h4>
                                <p class="text-[11px] sm:text-xs text-slate-600">Smart Progressive Overload dengan Riwayat Sesi Terakhir</p>
                            </div>
                            <span class="shrink-0 px-2.5 py-0.5 sm:py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black">Rest: 01:15</span>
                        </div>

                        <!-- Exercise Card Mockup -->
                        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2.5 sm:space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">1</div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs sm:text-sm text-slate-900 truncate">Barbell Bench Press</div>
                                        <div class="text-[10px] sm:text-[11px] text-slate-500 truncate">Dada (Chest) • Barbell</div>
                                    </div>
                                </div>
                                <span class="shrink-0 text-[10px] sm:text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 sm:py-1 rounded-md border border-emerald-100">
                                    + Rekor Baru (PR)
                                </span>
                            </div>

                            <!-- Sets Table Mockup (Clean Grid, Perfectly Sized for Mobile) -->
                            <div class="space-y-1 text-xs">
                                <div class="grid grid-cols-5 text-center text-[10px] sm:text-[11px] font-bold text-slate-500 p-1.5 sm:p-2 bg-slate-50 rounded-xl">
                                    <span>SET</span>
                                    <span>TERAKHIR</span>
                                    <span>BEBAN</span>
                                    <span>REPS</span>
                                    <span>STATUS</span>
                                </div>
                                <div class="grid grid-cols-5 text-center items-center text-[11px] sm:text-xs font-semibold p-1.5 sm:p-2 rounded-xl bg-emerald-50/60 border border-emerald-200 text-slate-900">
                                    <span class="font-bold text-emerald-800">Set 1</span>
                                    <span class="text-slate-500 text-[10px] sm:text-xs">75kg × 10</span>
                                    <span class="font-bold">80 kg</span>
                                    <span class="font-bold">10 reps</span>
                                    <span class="text-emerald-600 font-black text-[10px] sm:text-xs">✓ Selesai</span>
                                </div>
                                <div class="grid grid-cols-5 text-center items-center text-[11px] sm:text-xs font-semibold p-1.5 sm:p-2 rounded-xl bg-blue-50/60 border border-blue-200 text-slate-900">
                                    <span class="font-bold text-blue-800">Set 2</span>
                                    <span class="text-slate-500 text-[10px] sm:text-xs">75kg × 8</span>
                                    <span class="font-bold">80 kg</span>
                                    <span class="font-bold">8 reps</span>
                                    <span class="text-blue-600 font-black text-[10px] sm:text-xs">⏱ 00:45</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Summary Metrics -->
                        <div class="grid grid-cols-3 gap-1.5 sm:gap-2 text-center text-xs">
                            <div class="p-2 sm:p-2.5 bg-white rounded-xl border border-slate-200">
                                <div class="text-[10px] text-slate-500 uppercase">Total Beban</div>
                                <div class="text-slate-900 font-black text-xs sm:text-sm mt-0.5">3,450 kg</div>
                            </div>
                            <div class="p-2 sm:p-2.5 bg-white rounded-xl border border-slate-200">
                                <div class="text-[10px] text-slate-500 uppercase">Durasi Sesi</div>
                                <div class="text-slate-900 font-black text-xs sm:text-sm mt-0.5">42 menit</div>
                            </div>
                            <div class="p-2 sm:p-2.5 bg-white rounded-xl border border-slate-200">
                                <div class="text-[10px] text-slate-500 uppercase">Rest Timer</div>
                                <div class="text-blue-600 font-black text-xs sm:text-sm mt-0.5">Auto Sound</div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: MUSCLE HEATMAP PREVIEW -->
                    <div x-show="activeTab === 'heatmap'" x-cloak class="w-full max-w-3xl space-y-3 sm:space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1 sm:gap-2">
                            <div>
                                <h4 class="font-display font-black text-sm sm:text-base text-slate-900">Peta Otot Interaktif (Muscle Heatmap)</h4>
                                <p class="text-[11px] sm:text-xs text-slate-600">Visualisasi bagian tubuh yang terlatih dalam 7 hari terakhir</p>
                            </div>
                            <span class="shrink-0 px-2.5 py-0.5 sm:py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[11px] sm:text-xs font-bold">Fokus: Dada & Punggung</span>
                        </div>

                        <div class="bg-white p-3.5 sm:p-6 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4 sm:gap-6">
                            <!-- Visual Anatomy Representation (Side-by-side on mobile) -->
                            <div class="grid grid-cols-2 gap-2.5 sm:gap-4 w-full md:w-auto shrink-0 justify-center">
                                <div class="rounded-2xl bg-slate-900 p-2.5 sm:p-3 flex flex-col justify-between text-white relative overflow-hidden shadow-md h-40 sm:h-48">
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Tampak Depan</div>
                                    <div class="space-y-1">
                                        <div class="px-1.5 py-0.5 bg-amber-500 rounded text-[9px] sm:text-[10px] font-bold">Dada: 12 Set</div>
                                        <div class="px-1.5 py-0.5 bg-blue-600 rounded text-[9px] sm:text-[10px] font-bold">Bahu: 8 Set</div>
                                        <div class="px-1.5 py-0.5 bg-emerald-600 rounded text-[9px] sm:text-[10px] font-bold">Kaki: 6 Set</div>
                                    </div>
                                    <div class="text-[8px] sm:text-[9px] text-slate-400">Anterior</div>
                                </div>

                                <div class="rounded-2xl bg-slate-900 p-2.5 sm:p-3 flex flex-col justify-between text-white relative overflow-hidden shadow-md h-40 sm:h-48">
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Tampak Belakang</div>
                                    <div class="space-y-1">
                                        <div class="px-1.5 py-0.5 bg-amber-500 rounded text-[9px] sm:text-[10px] font-bold">Punggung: 14 Set</div>
                                        <div class="px-1.5 py-0.5 bg-blue-600 rounded text-[9px] sm:text-[10px] font-bold">Triceps: 8 Set</div>
                                        <div class="px-1.5 py-0.5 bg-slate-700 rounded text-[9px] sm:text-[10px] font-bold">Hamstring: Rehat</div>
                                    </div>
                                    <div class="text-[8px] sm:text-[9px] text-slate-400">Posterior</div>
                                </div>
                            </div>

                            <div class="text-left space-y-1.5 sm:space-y-2 text-xs w-full">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">Bagaimana Heatmap Bekerja?</div>
                                <p class="text-slate-600 leading-relaxed text-[11px] sm:text-xs">Setiap kali Anda mencatat set latihan, algoritma GerakIn menghitung intensitas otot secara real-time. Bagian tubuh yang butuh pemulihan akan ditandai agar tidak terjadi overtraining.</p>
                                <div class="pt-1 flex items-center gap-2 text-[10px] sm:text-[11px] font-semibold text-blue-600">
                                    <span>Tersedia untuk siluet pria dan wanita</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: ANALYTICS & PROGRESS -->
                    <div x-show="activeTab === 'analytics'" x-cloak class="w-full max-w-3xl space-y-3 sm:space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1 sm:gap-2">
                            <div>
                                <h4 class="font-display font-black text-sm sm:text-base text-slate-900">Grafik Konsistensi & Riwayat Beban</h4>
                                <p class="text-[11px] sm:text-xs text-slate-600">Pantau perkembangan One Rep Max (1RM) dan streak mingguan</p>
                            </div>
                            <span class="shrink-0 px-2.5 py-0.5 sm:py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">🔥 6 Minggu Streak</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
                            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200">
                                <div class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-500">Target Mingguan</div>
                                <div class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-0.5">4 / 4</div>
                                <div class="text-[10px] sm:text-[11px] text-emerald-600 font-bold mt-0.5">✓ Target Terpenuhi</div>
                            </div>

                            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200">
                                <div class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-500">Volume Angkatan</div>
                                <div class="font-display font-black text-2xl sm:text-3xl text-blue-600 mt-0.5">48.2 <span class="text-sm">ton</span></div>
                                <div class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5">+12% dari bulan lalu</div>
                            </div>

                            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200">
                                <div class="text-[10px] sm:text-[11px] font-bold uppercase text-slate-500">Total Sesi Lari</div>
                                <div class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-0.5">18 <span class="text-sm font-semibold">kali</span></div>
                                <div class="text-[10px] sm:text-[11px] text-blue-600 font-bold mt-0.5">84.5 km tercatat</div>
                            </div>
                        </div>

                        <!-- Progress Bar Simulation -->
                        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 space-y-1.5 sm:space-y-2">
                            <div class="flex justify-between text-[11px] sm:text-xs font-bold">
                                <span class="text-slate-700">Progres Beban Bench Press (Target: 100 kg)</span>
                                <span class="text-blue-600">85 kg (85%)</span>
                            </div>
                            <div class="w-full h-2.5 sm:h-3 rounded-full bg-slate-100 overflow-hidden">
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
                <div id="strava-running" class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Pelacak Lari GPS ala Strava</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Lacak lintasan lari real-time di atas peta interaktif. Dilengkapi kalkulator kecepatan (min/km), pecahan per kilometer (lap splits), kalori, dan mode simulasi demo.
                    </p>
                </div>

                <!-- Feature 2: Smart Gym Logger -->
                <div id="gym-logger" class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Smart Workout Logger</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Mengingat beban dan repetisi dari sesi sebelumnya secara otomatis. Ketahui kapan saatnya menaikkan beban (*progressive overload*) tanpa perlu membuka buku catatan.
                    </p>
                </div>

                <!-- Feature 3: Auto Rest Timer -->
                <div class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Rest Timer Otomatis</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Timer hitung mundur menyala otomatis begitu Anda mencentang set selesai. Disertai notifikasi suara dan getar haptic saat waktu istirahat habis.
                    </p>
                </div>

                <!-- Feature 4: Interactive Muscle Heatmap -->
                <div id="muscle-heatmap" class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Muscle Heatmap Anatomi</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Lihat visual tubuh yang menyala berdasarkan volume latihan mingguan Anda. Memastikan keseimbangan latihan antara tubuh bagian atas dan bawah.
                    </p>
                </div>

                <!-- Feature 5: 100+ Exercise Library -->
                <div class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">100+ Database Gerakan & Kustom</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Database lengkap gerakan Barbell, Dumbbell, Mesin, hingga Kalistenik tubuh. Bebas menambahkan gerakan kustom Anda sendiri kapan saja.
                    </p>
                </div>

                <!-- Feature 6: Offline PWA & Zero Ads -->
                <div id="pwa-offline" class="p-6 rounded-3xl bg-blue-50/40 hover:bg-blue-50/80 border border-blue-100 hover:border-blue-300 transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-200/60 text-blue-600 flex items-center justify-center mb-4 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900 mb-1.5">Offline First & PWA Installable</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pasang di homescreen iOS/Android layaknya aplikasi native. Tetap mencatat dengan lancar saat Anda berlatih di basement gym tanpa internet.
                    </p>
                </div>
            </div>

            <!-- Dedicated PWA Install Banner Card -->
            <div class="mt-8 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-1.5 text-center sm:text-left">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-blue-100 text-[11px] font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Instalasi Cepat • Progressive Web App</span>
                    </div>
                    <h3 class="font-display font-black text-xl sm:text-2xl text-white">
                        Pasang GerakIn Langsung ke Layar Utama HP Anda
                    </h3>
                    <p class="text-xs sm:text-sm text-blue-100 max-w-xl">
                        Tanpa perlu unduh ratusan MB dari toko aplikasi. GerakIn langsung terpasang di HP Anda, ringan, cepat, dan siap digunakan saat sinyal gym mati.
                    </p>
                </div>
                <button type="button"
                        @click="window.triggerPwaInstall ? window.triggerPwaInstall() : (pwaModalOpen = true)"
                        class="shrink-0 px-6 py-3.5 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 font-display font-black text-xs sm:text-sm shadow-md active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Pasang Aplikasi Sekarang</span>
                </button>
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

    <!-- PWA Installation Guide Modal -->
    <div x-show="pwaModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div @click.away="pwaModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <img src="/icons/icon-72x72.png" alt="GerakIn" class="w-9 h-9 rounded-xl shadow-xs">
                    <div>
                        <h3 class="font-display font-black text-lg text-slate-900">Pasang GerakIn (PWA)</h3>
                        <p class="text-xs text-slate-500">Aplikasi siap jalan langsung dari layar utama</p>
                    </div>
                </div>
                <button type="button" @click="pwaModalOpen = false" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Direct Android APK Download Card -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-teal-500/10 border border-emerald-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.552 0 .9993.4486.9993 1.0003s-.4473 1.0003-.9993 1.0003m-11.046 0c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.5511 0 .9993.4486.9993 1.0003s-.4482 1.0003-.9993 1.0003m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.125 12 8.125s-3.5902.2866-5.1368.8248L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                            </svg>
                        </div>
                        <span class="font-display font-bold text-slate-900 text-sm">Unduh File APK Android</span>
                    </div>
                    <span class="text-[10px] bg-emerald-600 text-white font-extrabold px-2 py-0.5 rounded-full uppercase">APK v1.0</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Instal aplikasi Android resmi GerakIn secara mandiri tanpa Google Play Store. Dilengkapi fitur auto-update otomatis.
                </p>
                <button type="button"
                   @click="pwaModalOpen = false; startApkDownload()"
                   class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download APK GerakIn (app-release.apk)</span>
                </button>
            </div>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Atau Pasang PWA di Homescreen</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <!-- Device Instructions -->
            <div class="space-y-3 text-xs text-slate-700">
                <!-- iOS Instruction -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 text-xs">
                        <span>🍏 iPhone / iPad (Safari)</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1 text-slate-600 pl-0.5 leading-relaxed">
                        <li>Buka situs ini di browser <strong>Safari</strong>.</li>
                        <li>Ketuk ikon <strong>Bagikan (Share)</strong> di bilah bawah.</li>
                        <li>Pilih <strong>"Tambahkan ke Layar Utama" (Add to Home Screen)</strong>.</li>
                    </ol>
                </div>

                <!-- Android Instruction -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 text-xs">
                        <span>🤖 Android (Google Chrome)</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1 text-slate-600 pl-0.5 leading-relaxed">
                        <li>Buka situs ini di browser <strong>Chrome</strong>.</li>
                        <li>Ketuk ikon <strong>titik tiga (⋮)</strong> di pojok kanan atas.</li>
                        <li>Pilih <strong>"Pasang aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</li>
                    </ol>
                </div>

                <!-- Desktop Instruction -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 text-xs">
                        <span>💻 Laptop / Komputer (Chrome, Edge, Brave)</span>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Klik ikon <strong>⊕</strong> di sisi kanan address bar browser, lalu klik <strong>"Instal"</strong>.
                    </p>
                </div>
            </div>

            <div class="pt-2">
                <button type="button" @click="pwaModalOpen = false" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- APK Download Progress & Waiting Modal -->
    <div x-show="apkModalOpen"
         x-cloak
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div @click.away="apkModalOpen = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-5">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.552 0 .9993.4486.9993 1.0003s-.4473 1.0003-.9993 1.0003m-11.046 0c-.5511 0-.9993-.4486-.9993-1.0003s.4482-1.0003.9993-1.0003c.5511 0 .9993.4486.9993 1.0003s-.4482 1.0003-.9993 1.0003m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.125 12 8.125s-3.5902.2866-5.1368.8248L4.8409 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-black text-lg text-slate-900 leading-tight">Unduh GerakIn Android</h3>
                        <p class="text-xs text-slate-500 font-medium">Versi 1.0.0 • Paket APK Resmi (43 MB)</p>
                    </div>
                </div>
                <button type="button" @click="apkModalOpen = false" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Progress Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700 flex items-center gap-2">
                        <template x-if="apkStage !== 'completed'">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        </template>
                        <template x-if="apkStage === 'completed'">
                            <span class="inline-flex w-4 h-4 rounded-full bg-emerald-600 text-white items-center justify-center text-[10px]">✓</span>
                        </template>
                        <span x-text="apkStatusText"></span>
                    </span>
                    <span class="font-mono font-extrabold text-emerald-700" x-text="apkProgress + '%'"></span>
                </div>

                <!-- Animated Progress Bar -->
                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-300 ease-out shadow-xs"
                         :style="'width: ' + apkProgress + '%'"></div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <span>Berkas: gerakin-v1.0.0.apk</span>
                    <span>43.2 MB</span>
                </div>
            </div>

            <!-- Download Started Info & Instructions -->
            <div class="space-y-3">
                <template x-if="apkStage === 'completed'">
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1.5">
                        <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Unduhan Sedang Berjalan di Latar Belakang!</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed text-[11px]">
                            Berkas APK otomatis diunduh oleh peramban Anda. Silakan cek bilah notifikasi atas atau folder <strong>Download</strong> di HP Anda.
                        </p>
                    </div>
                </template>

                <!-- 3 Steps Installation Guide -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Cara Memasang di HP Android:</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1 text-slate-600 text-[11px] leading-relaxed">
                        <li>Buka file <strong>gerakin-v1.0.0.apk</strong> setelah unduhan selesai.</li>
                        <li>Jika Android memunculkan konfirmasi keamanan, pilih <strong>"Izinkan dari sumber ini"</strong> atau <strong>"Tetap Unduh / Instal"</strong>.</li>
                        <li>Tekan tombol <strong>Instal</strong>. Buka GerakIn dan siap digunakan.</li>
                    </ol>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center gap-2 pt-2">
                <button type="button"
                        @click="triggerFileDownload()"
                        class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh Ulang (Jika Belum Mulai)</span>
                </button>
                <button type="button"
                        @click="apkModalOpen = false"
                        class="py-3 px-5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</body>
</html>
