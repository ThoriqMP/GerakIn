<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Personalisasi Akun - GerakIn</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <link rel="icon" type="image/png" href="/favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4 selection:bg-blue-600 selection:text-white antialiased">
    <div class="w-full max-w-2xl mx-auto bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 my-6 relative overflow-hidden"
         x-data="{
            step: 1,
            gender: '{{ $profile->gender ?? 'male' }}',
            age: {{ $profile->age ?? 25 }},
            height_cm: {{ $profile->height_cm ?? 170 }},
            current_weight_kg: {{ $profile->current_weight_kg ?? 70 }},
            target_weight_kg: {{ $profile->target_weight_kg ?? 68 }},
            fitness_goal: '{{ $profile->fitness_goal ?? 'hypertrophy' }}',
            weekly_target_days: {{ $profile->weekly_target_days ?? 4 }},
            preferred_sports: ['gym'],
            preferred_unit: 'kg',
            default_rest_seconds: 90,

            toggleSport(sport) {
                if (this.preferred_sports.includes(sport)) {
                    if (this.preferred_sports.length > 1) {
                        this.preferred_sports = this.preferred_sports.filter(s => s !== sport);
                    }
                } else {
                    this.preferred_sports.push(sport);
                }
            }
         }">

        <!-- Wizard Top Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <img src="/icons/icon-72x72.png" alt="GerakIn" class="w-8 h-8 rounded-xl shadow-sm border border-slate-200">
                    <span class="font-display font-extrabold text-lg text-slate-900">Gerak<span class="text-blue-600">In</span> Setup</span>
                </div>
                <div class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                    Langkah <span x-text="step"></span> dari 3
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden flex">
                <div class="h-full bg-blue-600 transition-all duration-300"
                     :style="'width: ' + ((step / 3) * 100) + '%'"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
            @csrf
            <!-- Hidden inputs bound to x-data state -->
            <input type="hidden" name="gender" :value="gender">
            <input type="hidden" name="age" :value="age">
            <input type="hidden" name="height_cm" :value="height_cm">
            <input type="hidden" name="current_weight_kg" :value="current_weight_kg">
            <input type="hidden" name="target_weight_kg" :value="target_weight_kg">
            <input type="hidden" name="fitness_goal" :value="fitness_goal">
            <input type="hidden" name="weekly_target_days" :value="weekly_target_days">
            <input type="hidden" name="preferred_unit" :value="preferred_unit">
            <input type="hidden" name="default_rest_seconds" :value="default_rest_seconds">
            <template x-for="sport in preferred_sports" :key="sport">
                <input type="hidden" name="preferred_sports[]" :value="sport">
            </template>

            <!-- STEP 1: METRIK TUBUH DASAR -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="mb-6">
                    <h3 class="font-display font-extrabold text-2xl text-slate-900">1. Data Fisik & Metrik Tubuh</h3>
                    <p class="text-sm text-slate-600 mt-1">Data ini membantu kami menghitung estimasi volume latihan ideal dan progres bebanmu.</p>
                </div>

                <!-- Gender Selection -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="gender = 'male'"
                                :class="gender === 'male' ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'"
                                class="p-3.5 rounded-2xl border flex items-center justify-center gap-2 text-sm transition-all">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Pria</span>
                        </button>
                        <button type="button" @click="gender = 'female'"
                                :class="gender === 'female' ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700'"
                                class="p-3.5 rounded-2xl border flex items-center justify-center gap-2 text-sm transition-all">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Wanita</span>
                        </button>
                    </div>
                </div>

                <!-- Numeric Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Usia (Tahun)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="if(age > 14) age--" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">-</button>
                            <input type="number" x-model.number="age" class="w-full text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-lg focus:outline-none focus:border-blue-500">
                            <button type="button" @click="if(age < 90) age++" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tinggi Badan (cm)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="if(height_cm > 120) height_cm--" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">-</button>
                            <input type="number" x-model.number="height_cm" class="w-full text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-lg focus:outline-none focus:border-blue-500">
                            <button type="button" @click="if(height_cm < 230) height_cm++" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Berat Badan Saat Ini (kg)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="if(current_weight_kg > 35) current_weight_kg = +(current_weight_kg - 0.5).toFixed(1)" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">-</button>
                            <input type="number" step="0.1" x-model.number="current_weight_kg" class="w-full text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-lg focus:outline-none focus:border-blue-500">
                            <button type="button" @click="if(current_weight_kg < 250) current_weight_kg = +(current_weight_kg + 0.5).toFixed(1)" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Berat Badan (kg)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="if(target_weight_kg > 35) target_weight_kg = +(target_weight_kg - 0.5).toFixed(1)" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">-</button>
                            <input type="number" step="0.1" x-model.number="target_weight_kg" class="w-full text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-lg focus:outline-none focus:border-blue-500">
                            <button type="button" @click="if(target_weight_kg < 250) target_weight_kg = +(target_weight_kg + 0.5).toFixed(1)" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">+</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 2" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm flex items-center gap-2 transition-all">
                        <span>Lanjut ke Tujuan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: FITNESS GOAL -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="mb-6">
                    <h3 class="font-display font-extrabold text-2xl text-slate-900">2. Apa Tujuan Utama Olahragamu?</h3>
                    <p class="text-sm text-slate-600 mt-1">Pilih fokus latihan agar kami dapat menyiapkan template jadwal yang paling relevan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                    <!-- Goal 1: Hypertrophy -->
                    <div @click="fitness_goal = 'hypertrophy'"
                         :class="fitness_goal === 'hypertrophy' ? 'border-blue-600 bg-blue-50/70 shadow-sm' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'"
                         class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div x-show="fitness_goal === 'hypertrophy'" class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Muscle Hypertrophy</h4>
                            <p class="text-xs text-slate-600 mt-1">Membangun massa otot, menebalkan dada, bahu, dan punggung dengan progressive overload.</p>
                        </div>
                    </div>

                    <!-- Goal 2: Lose Fat -->
                    <div @click="fitness_goal = 'lose_fat'"
                         :class="fitness_goal === 'lose_fat' ? 'border-blue-600 bg-blue-50/70 shadow-sm' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'"
                         class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.173 1.956-1.874 3.18C7.576 7.49 6.84 8.77 6.27 9.873c-.02.04-.04.08-.06.12A6.002 6.002 0 004 15a6 6 0 1012 0c0-1.63-.647-3.11-1.696-4.207a1 1 0 00-.709-.293H13.5a1 1 0 01-.894-.553l-1.21-2.42z" clip-rule="evenodd"/></svg>
                            </div>
                            <div x-show="fitness_goal === 'lose_fat'" class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Bakar Lemak (Fat Loss)</h4>
                            <p class="text-xs text-slate-600 mt-1">Menurunkan kadar lemak tubuh dan mempertajam definisi otot dengan volume latihan teratur.</p>
                        </div>
                    </div>

                    <!-- Goal 3: Endurance -->
                    <div @click="fitness_goal = 'endurance'"
                         :class="fitness_goal === 'endurance' ? 'border-blue-600 bg-blue-50/70 shadow-sm' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'"
                         class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div x-show="fitness_goal === 'endurance'" class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Endurance & Lari</h4>
                            <p class="text-xs text-slate-600 mt-1">Meningkatkan kapasitas kardio, stamina tubuh, dan konsistensi jarak lari.</p>
                        </div>
                    </div>

                    <!-- Goal 4: Stay Active -->
                    <div @click="fitness_goal = 'stay_active'"
                         :class="fitness_goal === 'stay_active' ? 'border-blue-600 bg-blue-50/70 shadow-sm' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'"
                         class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div x-show="fitness_goal === 'stay_active'" class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Tetap Aktif & Sehat</h4>
                            <p class="text-xs text-slate-600 mt-1">Menjaga kebugaran jasmani, stamina kerja harian, dan kesehatan jangka panjang.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between gap-2.5 sm:gap-0 pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 1" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm text-center transition-all">
                        Kembali
                    </button>
                    <button type="button" @click="step = 3" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all">
                        <span>Lanjut ke Preferensi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 3: PREFERENSI & JADWAL -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="mb-6">
                    <h3 class="font-display font-extrabold text-2xl text-slate-900">3. Jadwal & Pengaturan Latihan</h3>
                    <p class="text-sm text-slate-600 mt-1">Tentukan target komitmen mingguan dan preferensi timer di gym.</p>
                </div>

                <!-- Preferred Sports -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Olahraga Favorit</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <button type="button" @click="toggleSport('gym')"
                                :class="preferred_sports.includes('gym') ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="p-3 rounded-2xl border flex flex-col items-center gap-1.5 text-xs transition-all">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Gym / Beban</span>
                        </button>
                        <button type="button" @click="toggleSport('running')"
                                :class="preferred_sports.includes('running') ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="p-3 rounded-2xl border flex flex-col items-center gap-1.5 text-xs transition-all">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Lari</span>
                        </button>
                        <button type="button" @click="toggleSport('calisthenics')"
                                :class="preferred_sports.includes('calisthenics') ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="p-3 rounded-2xl border flex flex-col items-center gap-1.5 text-xs transition-all">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <span>Kalistenik</span>
                        </button>
                        <button type="button" @click="toggleSport('cycling')"
                                :class="preferred_sports.includes('cycling') ? 'bg-blue-50 border-blue-600 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="p-3 rounded-2xl border flex flex-col items-center gap-1.5 text-xs transition-all">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sepeda</span>
                        </button>
                    </div>
                </div>

                <!-- Weekly Target Days -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Target Hari Latihan per Minggu</label>
                        <span class="font-display font-extrabold text-base text-blue-600" x-text="weekly_target_days + ' Hari / Minggu'"></span>
                    </div>
                    <div class="grid grid-cols-6 gap-2">
                        <template x-for="d in [2, 3, 4, 5, 6, 7]" :key="d">
                            <button type="button" @click="weekly_target_days = d"
                                    :class="weekly_target_days === d ? 'bg-blue-600 text-white font-extrabold shadow-sm' : 'bg-slate-50 text-slate-700 border border-slate-200'"
                                    class="py-2.5 rounded-xl font-display text-sm transition-all" x-text="d + 'x'">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Default Rest Timer & Preferred Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Default Rest Timer Antar Set</label>
                        <select x-model.number="default_rest_seconds" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm focus:outline-none focus:border-blue-500">
                            <option value="45">45 Detik (Intensif)</option>
                            <option value="60">60 Detik (1 Menit Standar)</option>
                            <option value="90">90 Detik (1.5 Menit Rekomendasi)</option>
                            <option value="120">120 Detik (2 Menit Strength)</option>
                            <option value="180">180 Detik (3 Menit Heavy Lift)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan Beban Utama</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="preferred_unit = 'kg'"
                                    :class="preferred_unit === 'kg' ? 'bg-blue-50 border-blue-600 text-blue-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                    class="py-2.5 rounded-xl border text-sm transition-all">
                                Kilogram (kg)
                            </button>
                            <button type="button" @click="preferred_unit = 'lbs'"
                                    :class="preferred_unit === 'lbs' ? 'bg-blue-50 border-blue-600 text-blue-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                    class="py-2.5 rounded-xl border text-sm transition-all">
                                Pounds (lbs)
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between gap-2.5 sm:gap-0 pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 2" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm text-center transition-all">
                        Kembali
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                        <span>Selesaikan & Buka Dashboard</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
