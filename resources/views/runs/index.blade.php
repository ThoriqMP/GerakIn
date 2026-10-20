@extends('layouts.app', ['title' => 'Riwayat Lari & GPS Tracker'])

@section('content')
<div class="space-y-6 pt-1">

    <!-- 1. Running Hero & Primary CTA Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-[11px] font-bold text-blue-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Pelacak Lari GPS GerakIn</span>
                </div>
                <h1 class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-2">
                    Lari & Kardio
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md">
                    Catat waktu tempuh, pace per kilometer, rute GPS interaktif, dan kalori yang terbakar secara presisi.
                </p>
            </div>

            <!-- Start Run CTA Button -->
            <div class="flex items-center gap-3">
                <a href="{{ route('runs.track') }}" class="px-6 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-sm shadow-md shadow-blue-500/25 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span>Mulai Lari (GPS)</span>
                </a>
            </div>
        </div>

        <!-- Weekly Summary Bar -->
        <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-600 font-medium">Jarak Minggu Ini:</span>
                <span class="font-black text-slate-900 text-sm">{{ number_format($thisWeekDistance, 2) }} km</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-600 font-medium">Total Sesi:</span>
                <span class="font-bold text-blue-600">{{ $totalRuns }} sesi lari</span>
            </div>
        </div>
    </div>

    <!-- 2. Lifetime Statistics Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Distance -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-600">Total Jarak</div>
            <div class="my-2">
                <span class="font-display font-black text-2xl text-slate-900">{{ number_format($totalDistance, 1) }}</span>
                <span class="text-xs font-bold text-blue-600">km</span>
            </div>
            <div class="text-[10px] text-slate-600">Semua riwayat</div>
        </div>

        <!-- Total Moving Time -->
        @php
            $totHours = floor($totalDurationSeconds / 3600);
            $totMins = floor(($totalDurationSeconds % 3600) / 60);
        @endphp
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-600">Total Waktu</div>
            <div class="my-2">
                <span class="font-display font-black text-2xl text-slate-900">{{ $totHours }}j {{ $totMins }}m</span>
            </div>
            <div class="text-[10px] text-slate-600">Durasi bergerak</div>
        </div>

        <!-- Longest Single Run -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-600">Lari Terjauh</div>
            <div class="my-2">
                <span class="font-display font-black text-2xl text-blue-600">{{ number_format($longestRun, 2) }}</span>
                <span class="text-xs font-bold text-slate-600">km</span>
            </div>
            <div class="text-[10px] text-slate-600">Rekor jarak terbaik</div>
        </div>

        <!-- Fastest Pace -->
        @php
            $fastestPaceFormatted = "--'--\" /km";
            if ($fastestPaceSeconds && $fastestPaceSeconds > 0) {
                $fMin = floor($fastestPaceSeconds / 60);
                $fSec = $fastestPaceSeconds % 60;
                $fastestPaceFormatted = sprintf("%d'%02d\" /km", $fMin, $fSec);
            }
        @endphp
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-600">Pace Tercepat</div>
            <div class="my-2">
                <span class="font-display font-black text-xl sm:text-2xl text-emerald-600">{{ $fastestPaceFormatted }}</span>
            </div>
            <div class="text-[10px] text-slate-600">Untuk jarak ≥ 1 km</div>
        </div>
    </div>

    <!-- 3. Activity History Feed -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="font-display font-bold text-base text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Aktivitas Lari</span>
            </h2>
            <span class="text-xs text-slate-600 font-semibold">{{ $runs->total() }} Sesi Terdata</span>
        </div>

        @if ($runs->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-3xl p-8 border border-dashed border-slate-300 text-center">
                <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-4 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-display font-black text-lg text-slate-900">Belum Ada Catatan Lari</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-sm mx-auto">
                    Mulai lari pertamamu dengan GPS tracker interaktif. Kamu juga bisa mencoba fitur demo tanpa harus keluar ruangan.
                </p>
                <div class="mt-5">
                    <a href="{{ route('runs.track') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <span>Mulai Lari Sekarang</span>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($runs as $run)
                    <a href="{{ route('runs.show', $run) }}" class="block bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">
                                        {{ $run->time_of_day }}
                                    </span>
                                    <span class="text-xs text-slate-600 font-medium">
                                        {{ $run->formatted_date }}
                                    </span>
                                </div>
                                <h3 class="font-display font-black text-base sm:text-lg text-slate-900 truncate group-hover:text-blue-600 transition-colors">
                                    {{ $run->title }}
                                </h3>
                            </div>

                            <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-blue-50 text-slate-600 group-hover:text-blue-600 flex items-center justify-center transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>

                        <!-- Run Metrics Grid -->
                        <div class="grid grid-cols-4 gap-2 pt-4 mt-3 border-t border-slate-100 text-center">
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-600">Jarak</div>
                                <div class="font-display font-black text-sm sm:text-base text-slate-900 mt-0.5">
                                    {{ number_format($run->distance_km, 2) }} <span class="text-[10px] text-blue-600 font-bold">km</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-600">Waktu</div>
                                <div class="font-display font-black text-sm sm:text-base text-slate-900 mt-0.5">
                                    {{ $run->formatted_duration }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-600">Avg Pace</div>
                                <div class="font-display font-black text-sm sm:text-base text-blue-600 mt-0.5">
                                    {{ $run->formatted_pace }}
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-600">Kalori</div>
                                <div class="font-display font-black text-sm sm:text-base text-slate-900 mt-0.5">
                                    {{ number_format($run->calories_burned) }} <span class="text-[10px] text-slate-600">kcal</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $runs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
