@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="space-y-6 pt-1" x-data="{ copyModalOpen: false, selectedSourceWorkoutId: '{{ $copyableWorkouts->first()->id ?? '' }}' }">

    <!-- 1. Hero Greeting & Consistency Bar -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-[11px] font-bold text-blue-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <span>Konsistensi Mingguan</span>
                </div>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-2">
                    Halo, {{ explode(' ', $user->name)[0] }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md">
                    {{ $completedThisWeek >= $targetDays ? 'Target mingguanmu sudah tercapai. Pertahankan ritme latihanmu!' : "Kamu sudah menyelesaikan {$completedThisWeek} dari target {$targetDays} hari latihan minggu ini." }}
                </p>
            </div>

            <!-- Streak Counter Badge -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-amber-900">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.173 1.956-1.874 3.18C7.576 7.49 6.84 8.77 6.27 9.873c-.02.04-.04.08-.06.12A6.002 6.002 0 004 15a6 6 0 1012 0c0-1.63-.647-3.11-1.696-4.207a1 1 0 00-.709-.293H13.5a1 1 0 01-.894-.553l-1.21-2.42z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <div class="font-display font-black text-xl text-slate-900">{{ $weeklyStreak }} <span class="text-xs font-semibold text-amber-700">Minggu</span></div>
                        <div class="text-[10px] text-amber-800 font-bold uppercase tracking-wider">Streak Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-5 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs font-semibold mb-2">
                <span class="text-slate-600">Target: {{ $completedThisWeek }}/{{ $targetDays }} Sesi Latihan</span>
                <span class="text-blue-600 font-bold">{{ $weeklyProgressPercent }}%</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden flex">
                <div class="h-full bg-blue-600 rounded-full transition-all duration-500"
                     style="width: {{ $weeklyProgressPercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- 2. Date Pill Carousel (Past, Today, Future tracking) -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-3 px-1">
            <div class="min-w-0">
                <h3 class="font-display font-bold text-sm sm:text-base text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Linimasa & Jadwal Latihan</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pilih tanggal untuk melihat riwayat atau menjadwalkan latihan</p>
            </div>

            @if ($copyableWorkouts->isNotEmpty())
                <button type="button" @click="copyModalOpen = true" class="self-start sm:self-auto shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-colors border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span>Salin Latihan</span>
                </button>
            @endif
        </div>

        <!-- Carousel Horizontal Scroll (No Native Scrollbar Track) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 pt-1 no-scrollbar -mx-1 px-1">
            @foreach ($datePills as $pill)
                <a href="{{ route('dashboard', ['date' => $pill['date']]) }}" 
                   class="flex-shrink-0 flex flex-col items-center justify-center min-w-[58px] py-2.5 px-2 rounded-2xl border transition-all text-center {{ $pill['isSelected'] ? 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-500/20' : ($pill['isToday'] ? 'bg-blue-50/60 text-blue-900 border-blue-300' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200') }}">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $pill['isSelected'] ? 'text-blue-100' : 'text-slate-600' }}">
                        {{ $pill['dayName'] }}
                    </span>
                    <span class="text-base font-black mt-0.5 {{ $pill['isSelected'] ? 'text-white' : 'text-slate-900' }}">
                        {{ $pill['dayNumber'] }}
                    </span>
                    <div class="h-2 flex items-center justify-center mt-1">
                        @if ($pill['hasWorkout'])
                            <span class="w-1.5 h-1.5 rounded-full {{ $pill['isSelected'] ? 'bg-white' : 'bg-blue-600' }}"></span>
                        @elseif ($pill['isToday'])
                            <span class="text-[8px] font-bold uppercase {{ $pill['isSelected'] ? 'text-blue-200' : 'text-blue-600' }}">Hari ini</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Selected Date Workouts Section -->
        <div class="mt-4 pt-3 border-t border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-1.5 text-xs font-semibold text-slate-700 mb-2.5">
                <span class="truncate">Latihan Pada: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd, D MMMM Y') }}</strong></span>
                @if ($selectedDateWorkouts->count() > 0)
                    <span class="text-blue-600 font-bold shrink-0">{{ $selectedDateWorkouts->count() }} Sesi Terdata</span>
                @endif
            </div>

            @if ($selectedDateWorkouts->isEmpty())
                <div class="p-4 rounded-2xl bg-slate-50 border border-dashed border-slate-300 text-center">
                    <p class="text-xs text-slate-600">Tidak ada sesi latihan di tanggal ini.</p>
                    <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
                        <a href="{{ route('workouts.start', ['date' => $selectedDate]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors">
                            <span>+ Mulai Latihan Baru</span>
                        </a>
                        @if ($copyableWorkouts->isNotEmpty())
                            <button type="button" @click="copyModalOpen = true" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 transition-colors">
                                <span>Salin dari Sesi Lain</span>
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($selectedDateWorkouts as $session)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 truncate">{{ $session->title }}</h4>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                        <span>{{ $session->started_at->format('H:i') }}</span>
                                        <span>•</span>
                                        <span>{{ round($session->duration_seconds / 60) }} mnt</span>
                                        <span>•</span>
                                        <span class="text-blue-600 font-semibold">{{ number_format($session->total_volume_kg) }} kg volume</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-auto shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60 justify-end">
                                <a href="{{ route('workouts.show', $session->id) }}" class="flex-1 sm:flex-none text-center px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('workouts.summary', $session->id) }}" class="flex-1 sm:flex-none text-center px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-colors">
                                    Ringkasan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 3. Quick Action CTA (Start Blank or Start Routine) -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
        <!-- Big Quick Start Workout Card -->
        <div class="sm:col-span-7 bg-white rounded-3xl p-5 border border-slate-200 flex flex-col justify-between shadow-sm">
            <div class="mb-4">
                <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                    Mulai Sesi
                </span>
                <h3 class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-2">
                    Siap Latihan Hari Ini?
                </h3>
                <p class="text-xs text-slate-600 mt-1">
                    Catat set, beban kg, timer istirahat, dan kalkulator PR otomatis tanpa iklan.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-2">
                <a href="{{ route('workouts.start') }}" class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs sm:text-sm shadow-sm flex items-center gap-2 active:scale-95 transition-all">
                    <span>Mulai Latihan Baru</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('routines.index') }}" class="px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm border border-slate-200 transition-colors">
                    Pilih Dari Jadwal
                </a>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="sm:col-span-5 grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 flex flex-col justify-between shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-600">Total Angkatan</div>
                <div class="my-2">
                    <span class="font-display font-black text-2xl text-slate-900">{{ number_format($totalVolumeKg) }}</span>
                    <span class="text-xs font-semibold text-blue-600">kg</span>
                </div>
                <div class="text-[10px] text-slate-600 flex items-center gap-1">
                    <span class="text-emerald-600 font-bold">+{{ number_format($thisWeekVolumeKg) }} kg</span> mg ini
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 flex flex-col justify-between shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-600">Rekor Baru (PR)</div>
                <div class="my-2">
                    <span class="font-display font-black text-2xl text-blue-600">{{ $totalPrCount }}</span>
                    <span class="text-xs font-semibold text-slate-600">PR</span>
                </div>
                <div class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                    Terus berkembang
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 flex flex-col justify-between shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-600">Total Sesi</div>
                <div class="my-2">
                    <span class="font-display font-black text-2xl text-slate-900">{{ $totalWorkoutsCount }}</span>
                    <span class="text-xs font-semibold text-slate-600">kali</span>
                </div>
                <div class="text-[10px] text-slate-600">Semua riwayat</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 flex flex-col justify-between shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-600">Rest Timer</div>
                <div class="my-2">
                    <span class="font-display font-black text-2xl text-blue-600">{{ $profile->default_rest_seconds ?? 90 }}</span>
                    <span class="text-xs font-semibold text-slate-600">detik</span>
                </div>
                <div class="text-[10px] text-slate-600">Default istirahat</div>
            </div>
        </div>
    </div>

    <!-- 4. Interactive Realistic Muscle Heatmap Component -->
    <x-muscle-heatmap :muscleHeat="$muscleHeat" :muscleSetsCount="$muscleSetsCount" :gender="$profile->gender ?? 'male'" />

    <!-- 5. Routine Templates -->
    @if ($routines->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-display font-bold text-base text-slate-900">Jadwal Rutin Kamu</h3>
                </div>
                <a href="{{ route('routines.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Kelola Jadwal &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                @foreach ($routines as $routine)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 flex flex-col justify-between shadow-sm hover:border-blue-300 transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $routine->color_tag ?? '#2563EB' }};"></span>
                                <span class="text-[10px] font-bold text-slate-600">{{ $routine->exercises->count() }} Gerakan</span>
                            </div>
                            <h4 class="font-display font-bold text-sm text-slate-900 leading-snug">{{ $routine->name }}</h4>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $routine->description ?? 'Rangkaian latihan terprogram.' }}</p>
                        </div>

                        <form method="POST" action="{{ route('workouts.start') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="routine_id" value="{{ $routine->id }}">
                            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-800 font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-slate-200">
                                <span>Mulai Routine Ini</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 6. Recent Workouts Feed -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <h3 class="font-display font-bold text-base text-slate-900">Riwayat Latihan Terakhir</h3>
            </div>
            <a href="{{ route('workouts.history') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                Lihat Semua &rarr;
            </a>
        </div>

        @if ($recentWorkouts->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-base">Belum Ada Sesi Latihan</h4>
                <p class="text-xs text-slate-600 mt-1 mb-4">Catat sesi pertamamu untuk mulai membangun riwayat dan statistik angkatan.</p>
                <a href="{{ route('workouts.start') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                    <span>Mulai Workout Pertama</span>
                </a>
            </div>
        @else
            <div class="space-y-2.5">
                @foreach ($recentWorkouts as $session)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-slate-300 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0 mt-0.5 sm:mt-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-display font-bold text-sm text-slate-900 flex flex-wrap items-center gap-2">
                                    <span class="truncate">{{ $session->title }}</span>
                                    @if ($session->sets->where('is_pr', true)->count() > 0)
                                        <span class="shrink-0 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                            {{ $session->sets->where('is_pr', true)->count() }} PR
                                        </span>
                                    @endif
                                </h4>
                                <div class="text-xs text-slate-600 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span>{{ $session->started_at->isoFormat('D MMM Y • HH:mm') }}</span>
                                    <span>•</span>
                                    <span>{{ round($session->duration_seconds / 60) }} mnt</span>
                                    <span>•</span>
                                    <span class="text-blue-600 font-semibold">{{ number_format($session->total_volume_kg) }} kg volume</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 justify-end">
                            <a href="{{ route('workouts.summary', $session->id) }}" class="flex-1 sm:flex-none text-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1 border border-slate-200 transition-colors">
                                <span>Kartu Ringkasan</span>
                            </a>
                            <a href="{{ route('workouts.show', $session->id) }}" class="flex-1 sm:flex-none text-center px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 7. Badges Showcase -->
    @if ($badges->isNotEmpty())
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-display font-bold text-base text-slate-900">Lencana Pencapaian</h3>
                </div>
                <a href="{{ route('analytics.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Lihat Galeri &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($badges as $badge)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <h5 class="font-display font-bold text-xs text-slate-900 truncate">{{ $badge->title }}</h5>
                            <p class="text-[10px] text-slate-600 mt-0.5 truncate">{{ $badge->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 8. Modal: Salin Latihan dari Tanggal Sebelumnya -->
    <div x-show="copyModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         style="display: none;">
        <div @click.away="copyModalOpen = false" class="bg-white rounded-3xl border border-slate-200 shadow-xl max-w-lg w-full p-5 sm:p-6 overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-base text-slate-900">Salin Sesi Latihan</h4>
                        <p class="text-xs text-slate-600">Duplikasi gerakan dan set dari riwayat latihan sebelumnya</p>
                    </div>
                </div>
                <button type="button" @click="copyModalOpen = false" class="text-slate-600 hover:text-slate-800 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('workouts.copy') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Sesi Latihan Sumber</label>
                    <select name="source_workout_id" x-model="selectedSourceWorkoutId" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 bg-white focus:outline-none focus:border-blue-500">
                        @foreach ($copyableWorkouts as $cw)
                            <option value="{{ $cw->id }}">
                                {{ $cw->title }} ({{ $cw->started_at->isoFormat('D MMM Y') }} • {{ $cw->sets->groupBy('exercise_id')->count() }} gerakan)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Tanggal Pelaksanaan</label>
                    <input type="date" name="target_date" value="{{ $selectedDate }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 bg-white focus:outline-none focus:border-blue-500">
                    <p class="text-[11px] text-slate-600 mt-1">Default adalah tanggal yang sedang kamu pilih di linimasa carousel.</p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="copyModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition-colors">
                        Salin ke Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
