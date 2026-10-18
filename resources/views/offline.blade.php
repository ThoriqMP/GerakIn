<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Kamu Sedang Offline - GerakIn</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-center items-center p-6 text-center selection:bg-blue-600 selection:text-white antialiased">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-2.828-6.364m0 0L3 3m2.464 5.464a5 5 0 017.072 0m0 0l2.828 2.828M12 18a1 1 0 100-2 1 1 0 000 2z"/></svg>
        </div>

        <h1 class="font-display font-black text-2xl text-slate-900">Mode Offline Aktif</h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
            Koneksi internet tidak terdeteksi (mungkin kamu sedang berada di basement gym atau luar jangkauan).
        </p>

        <div class="p-4 my-5 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2 text-slate-600">
            <div class="flex items-center gap-2 font-bold text-blue-700">
                <span>GerakIn PWA Tetap Bekerja:</span>
            </div>
            <p>1. Catatan set dan repetisi tetap tersimpan di memori perangkatmu.</p>
            <p>2. Begitu sinyal kembali online, data akan otomatis disinkronkan ke server.</p>
        </div>

        <button type="button" onclick="window.location.reload()"
                class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm active:scale-95 transition-all">
            Coba Muat Ulang Halaman
        </button>
    </div>
</body>
</html>
