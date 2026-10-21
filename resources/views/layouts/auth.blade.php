<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} - GerakIn Fitness</title>

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicon.png">

    <!-- Fonts & Vite -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-center items-center p-4 selection:bg-blue-600 selection:text-white antialiased">
    <div class="w-full max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200 my-6">
        <!-- Left Side: Clean Branding / Feature Highlights (Desktop Only) -->
        <div class="hidden md:flex md:col-span-5 bg-slate-50/80 p-8 sm:p-10 flex-col justify-between border-b md:border-b-0 md:border-r border-slate-200">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <img src="/icons/icon-96x96.png" alt="GerakIn Logo" class="w-12 h-12 rounded-2xl shadow-sm border border-slate-200">
                    <div>
                        <h1 class="font-display font-black text-2xl text-slate-900 tracking-tight flex items-center gap-1">
                            <span>Gerak</span><span class="text-blue-600">In</span>
                        </h1>
                        <p class="text-xs text-slate-600 font-medium">Workout & Habit Tracker</p>
                    </div>
                </div>

                <div class="space-y-3 my-6">
                    <h2 class="font-display font-bold text-2xl text-slate-900 leading-snug">
                        Mulai Bergerak, <br><span class="text-blue-600">Konsisten Setiap Hari.</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pencatat beban gym & lari dengan kalkulator PR otomatis, visualisasi heatmap otot manusia yang realistis, dan mode offline saat sinyal minim.
                    </p>
                </div>
            </div>

            <!-- Highlights -->
            <div class="pt-6 border-t border-slate-200 space-y-2.5 text-xs text-slate-600">
                <div class="flex items-center gap-2.5">
                    <div class="w-5 h-5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>100% Berfungsi Offline dengan PWA</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-5 h-5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>Muscle Heatmap Anatomi Realistis</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-5 h-5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>Rest timer otomatis & pelacakan PR</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Auth Form Container (Full Width on Mobile) -->
        <div class="col-span-1 md:col-span-7 p-6 sm:p-10 bg-white flex flex-col justify-center">
            <!-- Mobile Brand Header (Visible only on mobile) -->
            <div class="flex items-center gap-2.5 mb-5 md:hidden">
                <img src="/icons/icon-72x72.png" alt="GerakIn Logo" class="w-9 h-9 rounded-xl shadow-xs">
                <div>
                    <span class="font-display font-black text-xl text-slate-900 tracking-tight flex items-center">
                        <span>Gerak</span><span class="text-blue-600">In</span>
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium">Aplikasi Kebugaran & Lari</span>
                </div>
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>
