@extends('layouts.auth', ['title' => 'Masuk'])

@section('content')
<div>
    <div class="mb-6">
        <h2 class="font-display font-extrabold text-2xl text-slate-900">Selamat Datang!</h2>
        <p class="text-sm text-slate-600 mt-1">Masuk ke akun GerakIn kamu untuk melanjutkan progres latihan.</p>
    </div>

    <!-- Quick Demo Logins for Instant Testing -->
    <div class="p-3.5 mb-6 rounded-2xl bg-blue-50/70 border border-blue-200 flex flex-col gap-2">
        <div class="text-[11px] font-bold uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Mode Pengujian Cepat</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <a href="{{ route('demo.login') }}" class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                <span>Masuk: Akun Demo Budi</span>
            </a>
            <a href="{{ route('demo.new-user') }}" class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors border border-slate-300">
                <span>Tes Onboarding Baru</span>
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-3.5 mb-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
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
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
            <div class="relative">
                <input type="email" id="email" name="email" value="{{ old('email', 'budi@gerakin.app') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-600 focus:outline-none focus:border-blue-500 text-sm transition-all"
                       placeholder="nama@email.com">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
            </div>
            <div class="relative">
                <input type="password" id="password" name="password" value="password123" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-600 focus:outline-none focus:border-blue-500 text-sm transition-all"
                       placeholder="••••••••">
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-600">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                <span>Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm active:scale-[0.98] transition-all">
            Masuk ke GerakIn
        </button>
    </form>

    <div class="mt-6 text-center text-xs text-slate-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline ml-1">Daftar sekarang</a>
    </div>
</div>
@endsection
