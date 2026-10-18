@extends('layouts.auth', ['title' => 'Daftar Akun'])

@section('content')
<div x-data="{
    password: '',
    get strength() {
        let score = 0;
        if (this.password.length >= 8) score++;
        if (/[A-Z]/.test(this.password)) score++;
        if (/[0-9]/.test(this.password)) score++;
        if (/[^A-Za-z0-9]/.test(this.password)) score++;
        return score;
    }
}">
    <div class="mb-5">
        <h2 class="font-display font-extrabold text-2xl text-slate-900">Buat Akun Baru</h2>
        <p class="text-sm text-slate-600 mt-1">Langkah awal untuk konsistensi olahraga dan transformasi fisikmu.</p>
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

    <form method="POST" action="{{ route('register.post') }}" class="space-y-3.5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm"
                       placeholder="Nama Lengkap">
            </div>

            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm"
                       placeholder="username">
            </div>
        </div>

        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-600 focus:outline-none focus:border-blue-500 text-sm"
                   placeholder="nama@email.com">
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
            <input type="password" id="password" name="password" x-model="password" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-600 focus:outline-none focus:border-blue-500 text-sm"
                   placeholder="Minimal 8 karakter">
            <!-- Password Strength Bar -->
            <div class="mt-2 flex items-center gap-1.5" x-show="password.length > 0">
                <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 1 ? 'bg-red-500' : 'bg-slate-200'"></div>
                <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 2 ? 'bg-amber-500' : 'bg-slate-200'"></div>
                <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 3 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 4 ? 'bg-blue-600' : 'bg-slate-200'"></div>
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-600 focus:outline-none focus:border-blue-500 text-sm"
                   placeholder="Ulangi kata sandi">
        </div>

        <button type="submit" class="w-full py-3 px-4 mt-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm active:scale-[0.98] transition-all">
            Daftar & Atur Profil Olahraga
        </button>
    </form>

    <div class="mt-5 text-center text-xs text-slate-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline ml-1">Masuk sekarang</a>
    </div>
</div>
@endsection
