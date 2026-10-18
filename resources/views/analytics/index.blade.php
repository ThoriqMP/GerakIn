@extends('layouts.app', ['title' => 'Statistik & Analitik'])

@section('content')
<div class="space-y-6 pt-1 pb-16" x-data="{
    showBodyLogModal: false,
    init() {
        this.renderWeightChart();
        this.renderVolumeChart();
    },
    renderWeightChart() {
        const ctx = document.getElementById('weightChart');
        if (!ctx) return;
        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($weightChartLabels),
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: @json($weightChartData),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    }
                }
            }
        });
    },
    renderVolumeChart() {
        const ctx = document.getElementById('volumeChart');
        if (!ctx) return;
        new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($weeklyVolumeLabels),
                datasets: [{
                    label: 'Total Tonnage (kg)',
                    data: @json($weeklyVolumeData),
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    }
                }
            }
        });
    }
}">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-display font-black text-2xl text-slate-900">Statistik & Progres</h2>
            <p class="text-xs text-slate-600 mt-0.5">Pantau tren berat badan, total volume mingguan, dan 1RM.</p>
        </div>
        <button type="button" @click="showBodyLogModal = true" class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-transform active:scale-95 flex items-center gap-1.5 self-start sm:self-auto">
            <span>+ Catat BB</span>
        </button>
    </div>

    <!-- Lifetime Summary Stat Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-600">Total Angkatan</div>
            <div class="font-display font-black text-xl text-blue-600 mt-1">
                {{ number_format($totalVolumeKg) }} <span class="text-xs font-semibold text-slate-600">kg</span>
            </div>
            <div class="text-[10px] text-slate-600 mt-0.5">Total tonase terakumulasi</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-600">Total Sesi Latihan</div>
            <div class="font-display font-black text-xl text-slate-900 mt-1">
                {{ $totalSessionsCount }} <span class="text-xs font-semibold text-slate-600">kali</span>
            </div>
            <div class="text-[10px] text-slate-600 mt-0.5">Sesi selesai</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-600">Total Set Selesai</div>
            <div class="font-display font-black text-xl text-blue-600 mt-1">
                {{ $totalSetsCount }} <span class="text-xs font-semibold text-slate-600">set</span>
            </div>
            <div class="text-[10px] text-slate-600 mt-0.5">Beban & kardio</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-600">Jam Latihan</div>
            <div class="font-display font-black text-xl text-slate-900 mt-1">
                {{ $totalHours }} <span class="text-xs font-semibold text-slate-600">jam</span>
            </div>
            <div class="text-[10px] text-slate-600 mt-0.5">Waktu produktif</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- 1. Weekly Total Volume Tonnage Chart -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                        Total Tonnage (kg)
                    </span>
                    <span class="text-xs text-slate-600">8 Minggu Terakhir</span>
                </div>
                <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">Volume Latihan Mingguan</h3>
                <p class="text-xs text-slate-600 mt-0.5">Set × Repetisi × Beban (kg)</p>
            </div>
            <div class="h-56 mt-4">
                <canvas id="volumeChart"></canvas>
            </div>
        </div>

        <!-- 2. Body Weight Trend Chart -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                        Body Weight (kg)
                    </span>
                    <span class="text-xs text-slate-600">Tren Perubahan</span>
                </div>
                <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">Tren Berat Badan</h3>
                <p class="text-xs text-slate-600 mt-0.5">Target: {{ $user->profile?->target_weight_kg ?? 70 }} kg</p>
            </div>
            <div class="h-56 mt-4">
                <canvas id="weightChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. Estimated 1RM Progression for Big Lifts -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">Estimasi One-Rep Max (1RM)</h3>
                <p class="text-xs text-slate-600 mt-0.5">Kalkulasi beban maksimal angkatan 1 repetisi (Formula Epley).</p>
            </div>
            <span class="text-xs font-bold text-blue-600">Big Lifts</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($oneRmProgression as $lift)
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700">{{ $lift['muscle'] }}</span>
                        <h4 class="font-display font-bold text-sm text-slate-900 mt-1 truncate">{{ $lift['name'] }}</h4>
                        <div class="text-xs text-slate-600 mt-0.5">
                            Set Terbaik: {{ $lift['best_weight'] }}kg × {{ $lift['best_reps'] }}r
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-display font-black text-xl text-blue-600">
                            {{ $lift['estimated_1rm'] }} <span class="text-xs font-semibold text-slate-600">kg</span>
                        </div>
                        <span class="text-[10px] text-slate-600 uppercase font-semibold">Estimasi 1RM</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Badges Gallery -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">Galeri Lencana</h3>
                <p class="text-xs text-slate-600 mt-0.5">Lencana milestone konsistensi dan pemecahan PR.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            @foreach ($badges as $b)
                <div class="p-4 rounded-2xl border flex items-center gap-3.5 {{ $b['is_unlocked'] ? 'bg-white border-amber-300 shadow-sm' : 'bg-slate-50 border-slate-200 opacity-60' }}">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $b['is_unlocked'] ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-slate-200 text-slate-400' }}">
                        @if ($b['is_unlocked'])
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        @else
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-display font-bold text-sm text-slate-900 truncate">{{ $b['title'] }}</h4>
                            @if ($b['is_unlocked'])
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5 line-clamp-2">{{ $b['description'] }}</p>
                        @if ($b['is_unlocked'])
                            <div class="text-[10px] text-amber-700 font-bold mt-1">Dibuka: {{ $b['unlocked_at'] }}</div>
                        @else
                            <div class="text-[10px] text-slate-600 font-semibold mt-1">Belum Terbuka</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL: RECORD BODY WEIGHT LOG -->
    <div x-show="showBodyLogModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 max-w-md w-full shadow-xl relative"
             @click.away="showBodyLogModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-base text-slate-900">Catat Berat Badan</h3>
                </div>
                <button type="button" @click="showBodyLogModal = false" class="text-slate-600 hover:text-slate-900 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('analytics.body-log.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" value="{{ $user->profile?->current_weight_kg ?? 70 }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-base focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kadar Lemak % (Opsional)</label>
                        <input type="number" step="0.1" name="body_fat_percentage" placeholder="15.5"
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal</label>
                        <input type="date" name="logged_at" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Kondisi pagi sebelum sarapan..."
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showBodyLogModal = false" class="w-1/3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
