@extends('layouts.app', ['title' => $run->title])

@push('scripts')
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
<div class="space-y-5" x-data="{ deleteModalOpen: false }">

    <!-- Top Action Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('runs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Semua Riwayat Lari</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('runs.track') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-sm shadow-blue-500/20 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                <span>Lari Lagi</span>
            </a>

            <button type="button"
                    @click="deleteModalOpen = true"
                    class="p-2 rounded-xl bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 transition-colors"
                    title="Hapus Catatan Lari">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Main Activity Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-5">
        <!-- Title & Athlete Bio -->
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-[11px] font-bold text-blue-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <span>{{ $run->time_of_day }}</span>
                </div>
                <h1 class="font-display font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    {{ $run->title }}
                </h1>
                <p class="text-xs text-slate-600 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $run->formatted_date }}</span>
                </p>
            </div>

            <!-- Profile Avatar Thumbnail -->
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-extrabold text-sm uppercase shadow-xs shrink-0">
                {{ substr($run->user->name ?? 'G', 0, 1) }}
            </div>
        </div>

        <!-- Big 4-Metric Strava Hero Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- 1. Jarak -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Jarak Tempuh</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-1">
                    {{ number_format($run->distance_km, 2) }}
                    <span class="text-sm font-bold text-blue-600">km</span>
                </div>
            </div>

            <!-- 2. Waktu Gerak -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Waktu Gerak</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-1">
                    {{ $run->formatted_duration }}
                </div>
            </div>

            <!-- 3. Rata-rata Pace -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Avg Pace</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-blue-600 mt-1">
                    {{ $run->formatted_pace }}
                </div>
            </div>

            <!-- 4. Kalori Terbakar -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Kalori</div>
                <div class="font-display font-black text-2xl sm:text-3xl text-slate-900 mt-1">
                    {{ number_format($run->calories_burned) }}
                    <span class="text-sm font-bold text-slate-600">kcal</span>
                </div>
            </div>
        </div>

        <!-- Secondary Info Bar: Pace Terbaik & Elevasi/Catatan -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-blue-700 font-medium">Pace Terbaik:</span>
                <span class="font-black text-blue-900">{{ $run->formatted_best_pace }}</span>
            </div>
            @if ($run->notes)
                <div class="text-slate-600 italic">
                    "{{ $run->notes }}"
                </div>
            @endif
        </div>

        <!-- Interactive Route Replay Map -->
        @php
            $coords = $run->route_coordinates ?? [];
            $hasCoords = is_array($coords) && count($coords) > 1;
        @endphp

        <div class="rounded-3xl border border-slate-200 overflow-hidden relative shadow-xs">
            <div id="replayMap" class="w-full h-80 sm:h-96 bg-slate-100"></div>

            @if (!$hasCoords)
                <div class="absolute inset-0 bg-slate-50/90 backdrop-blur-xs flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <p class="font-bold text-sm text-slate-800">Sesi Lari Disimpan Tanpa Koordinat GPS</p>
                    <p class="text-xs text-slate-600 mt-1 max-w-sm">Data durasi, jarak, dan pace tetap tersimpan aman di database.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Kilometer Splits Breakdown Table (Strava Pacing Bars) -->
    @php
        $splits = $run->km_splits ?? [];
        $fastestSplitPace = 999999;
        if (!empty($splits)) {
            foreach ($splits as $s) {
                if (!empty($s['pace_seconds']) && $s['pace_seconds'] < $fastestSplitPace) {
                    $fastestSplitPace = $s['pace_seconds'];
                }
            }
        }
    @endphp

    @if (!empty($splits))
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-display font-black text-lg text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Pecahan Kilometer (Splits)</span>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">Analisis tempo lari per 1 kilometer</p>
                </div>
                <span class="text-xs font-extrabold text-blue-600 px-3 py-1 rounded-full bg-blue-50 border border-blue-100">
                    {{ count($splits) }} KM
                </span>
            </div>

            <div class="space-y-2.5">
                @foreach ($splits as $split)
                    @php
                        $splitPaceSec = $split['pace_seconds'] ?? 0;
                        $splitDurationSec = $split['duration_seconds'] ?? 0;
                        $isFastest = ($splitPaceSec > 0 && $splitPaceSec === $fastestSplitPace);

                        $paceMin = floor($splitPaceSec / 60);
                        $paceRemSec = $splitPaceSec % 60;
                        $formattedSplitPace = sprintf("%d'%02d\" /km", $paceMin, $paceRemSec);

                        $durMin = floor($splitDurationSec / 60);
                        $durRemSec = $splitDurationSec % 60;
                        $formattedSplitDuration = sprintf("%02d:%02d", $durMin, $durRemSec);

                        // Relative percentage for bar visualization (range between 3 min/km and 10 min/km)
                        $relativeWidth = 100 - min(80, max(15, (($splitPaceSec - 180) / 420) * 100));
                    @endphp

                    <div class="p-3 rounded-2xl border transition-colors {{ $isFastest ? 'bg-blue-50/80 border-blue-200' : 'bg-slate-50 border-slate-200/80' }}">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg font-black text-[11px] flex items-center justify-center {{ $isFastest ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $split['km'] ?? $loop->iteration }}
                                </span>
                                <span class="font-bold text-slate-800">Kilometer {{ $split['km'] ?? $loop->iteration }}</span>
                                @if ($isFastest)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                                        Split Tercepat
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="text-slate-600 font-medium">Waktu: <strong class="text-slate-900">{{ $formattedSplitDuration }}</strong></span>
                                <span class="font-black text-sm {{ $isFastest ? 'text-emerald-700' : 'text-blue-600' }}">
                                    {{ $formattedSplitPace }}
                                </span>
                            </div>
                        </div>

                        <!-- Pace Visual Bar -->
                        <div class="w-full h-2 rounded-full bg-slate-200/70 overflow-hidden flex">
                            <div class="h-full rounded-full transition-all duration-500 {{ $isFastest ? 'bg-emerald-500' : 'bg-blue-600' }}"
                                 style="width: {{ $relativeWidth }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="deleteModalOpen = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="font-display font-black text-lg text-slate-900">Hapus Aktivitas Lari?</h3>
            <p class="text-xs text-slate-600 mt-1">Sesi lari ini akan dihapus secara permanen dari riwayat kamu.</p>

            <form action="{{ route('runs.destroy', $run) }}" method="POST" class="mt-5 flex gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm shadow-rose-500/20">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@if ($hasCoords)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rawCoords = @json($coords);
    if (!rawCoords || rawCoords.length < 2) return;

    // Convert coords to [lat, lng] array
    const latLngs = rawCoords.map(pt => [pt[0], pt[1]]);

    const map = L.map('replayMap', {
        zoomControl: false,
        attributionControl: false
    });

    // Clean, 100% free OpenStreetMap standard tiles (No API key, No watermarks)
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Glowing polyline
    const polyline = L.polyline(latLngs, {
        color: '#2563EB',
        weight: 5,
        opacity: 0.95,
        lineJoin: 'round',
        lineCap: 'round'
    }).addTo(map);

    // Start Marker (Green Pin)
    const startIcon = L.divIcon({
        className: 'start-marker',
        html: `<div class="w-7 h-7 rounded-full bg-emerald-600 border-2 border-white shadow-md text-white flex items-center justify-center font-bold text-[10px]">GO</div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
    L.marker(latLngs[0], { icon: startIcon }).addTo(map).bindPopup('Titik Mulai');

    // Finish Marker (Red/Checker Pin)
    const finishIcon = L.divIcon({
        className: 'finish-marker',
        html: `<div class="w-7 h-7 rounded-full bg-rose-600 border-2 border-white shadow-md text-white flex items-center justify-center font-bold text-[10px]">FIN</div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
    L.marker(latLngs[latLngs.length - 1], { icon: finishIcon }).addTo(map).bindPopup('Titik Selesai');

    // Auto fit map bounds
    map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
});
</script>
@endif
@endsection
