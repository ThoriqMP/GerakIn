@extends('layouts.app', ['title' => 'Profil & Pengaturan'])

@section('content')
<div class="space-y-6 pt-1 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-display font-black text-2xl text-slate-900">Profil & Pengaturan</h2>
            <p class="text-xs text-slate-600 mt-0.5">Kustomisasi preferensi latihan, metrik tubuh, dan akun.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="self-start sm:self-auto">
            @csrf
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 font-bold text-xs border border-slate-200 transition-colors">
                Keluar (Logout)
            </button>
        </form>
    </div>

    <!-- User Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-display font-black text-2xl shadow-sm">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-display font-black text-lg sm:text-xl text-slate-900">{{ $user->name }}</h3>
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-bold uppercase">
                        {{ ucfirst($profile->accent_badge ?? 'lightning') }}
                    </span>
                </div>
                <div class="text-xs text-slate-600 mt-0.5">{{ '@' . $user->username }} • {{ $user->email }}</div>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-center">
            <a href="{{ route('profile.export.json') }}" class="px-4 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 flex items-center gap-1.5 transition-colors shadow-sm">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Backup Data (JSON)</span>
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Edit Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Akun & Identitas -->
            <div>
                <h4 class="font-display font-bold text-base text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-blue-600">#</span>
                    <span>Informasi Akun</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Fisik & Target -->
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-display font-bold text-base text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-blue-600">#</span>
                    <span>Metrik Fisik & Target</span>
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="male" {{ ($profile->gender ?? '') === 'male' ? 'selected' : '' }}>Pria</option>
                            <option value="female" {{ ($profile->gender ?? '') === 'female' ? 'selected' : '' }}>Wanita</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Usia</label>
                        <input type="number" name="age" value="{{ old('age', $profile->age ?? 25) }}" required
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tinggi (cm)</label>
                        <input type="number" name="height_cm" value="{{ old('height_cm', $profile->height_cm ?? 170) }}" required
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target BB (kg)</label>
                        <input type="number" step="0.1" name="target_weight_kg" value="{{ old('target_weight_kg', $profile->target_weight_kg ?? 68) }}" required
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berat Badan Saat Ini (kg)</label>
                        <input type="number" step="0.1" name="current_weight_kg" value="{{ old('current_weight_kg', $profile->current_weight_kg ?? 70) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tujuan Utama (Fitness Goal)</label>
                        <select name="fitness_goal" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="hypertrophy" {{ ($profile->fitness_goal ?? '') === 'hypertrophy' ? 'selected' : '' }}>Muscle Hypertrophy / Bulking</option>
                            <option value="lose_fat" {{ ($profile->fitness_goal ?? '') === 'lose_fat' ? 'selected' : '' }}>Fat Loss / Bakar Lemak</option>
                            <option value="endurance" {{ ($profile->fitness_goal ?? '') === 'endurance' ? 'selected' : '' }}>Endurance & Kardio</option>
                            <option value="stay_active" {{ ($profile->fitness_goal ?? '') === 'stay_active' ? 'selected' : '' }}>Stay Active & Sehat</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Preferensi Workout & Timer -->
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-display font-bold text-base text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-blue-600">#</span>
                    <span>Preferensi Aplikasi & Timer</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Latihan / Minggu</label>
                        <select name="weekly_target_days" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            @for ($i = 2; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ ($profile->weekly_target_days ?? 4) == $i ? 'selected' : '' }}>{{ $i }} Hari per Minggu</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Default Rest Timer</label>
                        <select name="default_rest_seconds" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="45" {{ ($profile->default_rest_seconds ?? 90) == 45 ? 'selected' : '' }}>45 Detik</option>
                            <option value="60" {{ ($profile->default_rest_seconds ?? 90) == 60 ? 'selected' : '' }}>60 Detik (1 Menit)</option>
                            <option value="90" {{ ($profile->default_rest_seconds ?? 90) == 90 ? 'selected' : '' }}>90 Detik (1.5 Menit)</option>
                            <option value="120" {{ ($profile->default_rest_seconds ?? 90) == 120 ? 'selected' : '' }}>120 Detik (2 Menit)</option>
                            <option value="180" {{ ($profile->default_rest_seconds ?? 90) == 180 ? 'selected' : '' }}>180 Detik (3 Menit)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Satuan Beban</label>
                        <select name="preferred_unit" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="kg" {{ ($profile->preferred_unit ?? 'kg') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                            <option value="lbs" {{ ($profile->preferred_unit ?? '') === 'lbs' ? 'selected' : '' }}>Pounds (lbs)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tema Tampilan</label>
                        <select name="theme" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="light" {{ ($profile->theme ?? 'light') === 'light' ? 'selected' : '' }}>Clean Light Mode (Default)</option>
                            <option value="dark" {{ ($profile->theme ?? '') === 'dark' ? 'selected' : '' }}>Dark Mode</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lencana Profil (Accent Badge)</label>
                        <select name="accent_badge" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="lightning" {{ ($profile->accent_badge ?? 'lightning') === 'lightning' ? 'selected' : '' }}>Lightning (Petir Kecepatan)</option>
                            <option value="flame" {{ ($profile->accent_badge ?? '') === 'flame' ? 'selected' : '' }}>Flame (Semangat Membara)</option>
                            <option value="titan" {{ ($profile->accent_badge ?? '') === 'titan' ? 'selected' : '' }}>Titan (Kekuatan Beban)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 4: Ganti Sandi -->
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-display font-bold text-base text-slate-900 mb-3 flex items-center gap-2">
                    <span class="text-slate-600">#</span>
                    <span>Ubah Sandi (Kosongkan bila tidak diubah)</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sandi Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi sandi baru"
                               class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-sm active:scale-95 transition-all">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
