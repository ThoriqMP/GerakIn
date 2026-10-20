@extends('layouts.app', ['title' => 'GPS Running Tracker'])

@push('scripts')
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
<div class="space-y-4" x-data="runTracker({{ $userWeightKg }})" x-init="initTracker()">

    <!-- Top Navigation & Status -->
    <div class="flex items-center justify-between bg-white rounded-2xl p-3 border border-slate-200 shadow-xs">
        <a href="{{ route('runs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 px-2.5 py-1.5 rounded-xl hover:bg-slate-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Riwayat Lari</span>
        </a>

        <div class="flex items-center gap-2">
            <!-- Mode Indicator -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                 :class="{
                     'bg-slate-100 text-slate-700': state === 'idle',
                     'bg-emerald-50 text-emerald-700 border border-emerald-200': state === 'running',
                     'bg-amber-50 text-amber-700 border border-amber-200': state === 'paused',
                     'bg-blue-50 text-blue-700 border border-blue-200': state === 'demo'
                 }">
                <span class="w-2 h-2 rounded-full"
                      :class="{
                          'bg-slate-400': state === 'idle',
                          'bg-emerald-500 animate-pulse': state === 'running',
                          'bg-amber-500': state === 'paused',
                          'bg-blue-600 animate-pulse': state === 'demo'
                      }"></span>
                <span x-text="statusLabel"></span>
            </div>

            <!-- Demo Simulation Toggle Button -->
            <button type="button"
                    @click="toggleDemoMode()"
                    :disabled="state === 'running'"
                    class="px-2.5 py-1 text-[11px] font-bold rounded-xl border transition-all"
                    :class="isDemo ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'"
                    title="Uji coba pelacakan rute tanpa harus keluar ruangan">
                <span x-text="isDemo ? 'Matikan Demo' : 'Mode Simulasi Demo'"></span>
            </button>
        </div>
    </div>

    <!-- Live Map Card -->
    <div class="bg-white rounded-3xl p-2 sm:p-3 border border-slate-200 shadow-sm relative overflow-hidden">
        <div id="map" class="w-full h-72 sm:h-96 rounded-2xl z-0 bg-slate-100 relative">
            <!-- GPS Signal Overlay Notice -->
            <div x-show="!gpsLocked && !isDemo" x-cloak class="absolute top-3 left-3 right-3 sm:right-auto z-[400] bg-white/90 backdrop-blur-md px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <span x-text="gpsStatusMessage">Mencari sinyal GPS...</span>
            </div>

            <!-- Re-center Map Button -->
            <button type="button"
                    @click="centerMapOnUser()"
                    class="absolute bottom-3 right-3 z-[400] w-10 h-10 rounded-xl bg-white/95 backdrop-blur-md border border-slate-200 text-slate-700 hover:text-blue-600 shadow-md flex items-center justify-center transition-transform active:scale-95"
                    title="Pusatkan Peta">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
            </button>
        </div>
    </div>

    <!-- High-Contrast HUD Metrics (Strava-like telemetry) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <!-- Big Distance Metric -->
        <div class="text-center pb-4 border-b border-slate-100">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-600">Jarak Tempuh</div>
            <div class="font-display font-black text-5xl sm:text-6xl text-slate-900 tracking-tight mt-1">
                <span x-text="distanceKm.toFixed(2)">0.00</span>
                <span class="text-xl sm:text-2xl text-blue-600 font-bold ml-1">KM</span>
            </div>
        </div>

        <!-- 3-Column Telemetry Grid -->
        <div class="grid grid-cols-3 gap-2 sm:gap-4 pt-4 text-center">
            <!-- Moving Time -->
            <div class="p-2 sm:p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Waktu</div>
                <div class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-1" x-text="formattedDuration">
                    00:00
                </div>
            </div>

            <!-- Current / Split Pace -->
            <div class="p-2 sm:p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Pace Saat Ini</div>
                <div class="font-display font-black text-xl sm:text-2xl text-blue-600 mt-1" x-text="formattedCurrentPace">
                    --'--"
                </div>
                <div class="text-[9px] text-slate-600 font-semibold">/km</div>
            </div>

            <!-- Average Pace -->
            <div class="p-2 sm:p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-600">Rata-rata</div>
                <div class="font-display font-black text-xl sm:text-2xl text-slate-900 mt-1" x-text="formattedAvgPace">
                    --'--"
                </div>
                <div class="text-[9px] text-slate-600 font-semibold">/km</div>
            </div>
        </div>

        <!-- Secondary Metrics Row: Calories & Best Pace -->
        <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-100 text-xs">
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-600 font-medium">Kalori Terbakar</span>
                <span class="font-extrabold text-slate-900"><span x-text="caloriesBurned">0</span> kcal</span>
            </div>
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-600 font-medium">Pace Terbaik</span>
                <span class="font-extrabold text-emerald-600" x-text="formattedBestPace">--'--" /km</span>
            </div>
        </div>
    </div>

    <!-- Live Kilometer Splits Card (Strava Split Breakdown) -->
    <div x-show="kmSplits.length > 0" x-cloak class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-display font-bold text-sm text-slate-900 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Pecahan Kilometer (Splits)</span>
            </h3>
            <span class="text-xs font-semibold text-slate-600"><span x-text="kmSplits.length"></span> KM Tercatat</span>
        </div>

        <div class="space-y-2">
            <template x-for="(split, index) in kmSplits" :key="index">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 font-black flex items-center justify-center text-[11px]" x-text="split.km"></span>
                        <span class="font-bold text-slate-800">KM <span x-text="split.km"></span></span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="text-slate-600 text-[10px]">Waktu: </span>
                            <span class="font-bold text-slate-900" x-text="formatSeconds(split.duration_seconds)"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-600 text-[10px]">Pace: </span>
                            <span class="font-black text-blue-600" x-text="formatPace(split.pace_seconds)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Primary Control Buttons (Start / Pause / Resume / Finish) -->
    <div class="sticky bottom-20 z-30">
        <div class="p-3 bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 shadow-lg max-w-lg mx-auto">
            <!-- Idle State: Start Button -->
            <div x-show="state === 'idle'" class="flex gap-2">
                <button type="button"
                        @click="startRun()"
                        class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-display font-black text-base shadow-md shadow-blue-500/25 flex items-center justify-center gap-2 transition-all">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span>Mulai Lari</span>
                </button>
            </div>

            <!-- Running State: Pause Button -->
            <div x-show="state === 'running' || state === 'demo'" class="grid grid-cols-2 gap-3">
                <button type="button"
                        @click="pauseRun()"
                        class="py-3.5 px-4 rounded-2xl bg-amber-500 hover:bg-amber-600 active:scale-98 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Jeda Lari</span>
                </button>

                <button type="button"
                        @click="openFinishModal()"
                        class="py-3.5 px-4 rounded-2xl bg-rose-600 hover:bg-rose-700 active:scale-98 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/></svg>
                    <span>Selesai & Simpan</span>
                </button>
            </div>

            <!-- Paused State: Resume or Finish -->
            <div x-show="state === 'paused'" class="grid grid-cols-2 gap-3">
                <button type="button"
                        @click="resumeRun()"
                        class="py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span>Lanjutkan Lari</span>
                </button>

                <button type="button"
                        @click="openFinishModal()"
                        class="py-3.5 px-4 rounded-2xl bg-rose-600 hover:bg-rose-700 active:scale-98 text-white font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/></svg>
                    <span>Selesai</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Countdown Modal (3, 2, 1, GO!) -->
    <div x-show="isCountingDown" x-cloak class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="text-center text-white">
            <div class="font-display font-black text-8xl sm:text-9xl text-blue-400 animate-bounce" x-text="countdownNumber">
                3
            </div>
            <div class="text-lg font-bold tracking-wider mt-4 uppercase text-slate-200">
                Bersiap untuk lari...
            </div>
        </div>
    </div>

    <!-- Finish & Save Activity Modal -->
    <div x-show="showFinishModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div @click.away="showFinishModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 my-8">
            <div class="text-center mb-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-display font-black text-2xl text-slate-900">Lari Hebat Selesai!</h3>
                <p class="text-xs text-slate-600 mt-1">Simpan sesi lari ini ke riwayat aktivitas GerakIn kamu.</p>
            </div>

            <!-- Mini Summary Stats in Modal -->
            <div class="grid grid-cols-3 gap-2 p-3 rounded-2xl bg-slate-50 border border-slate-200 text-center mb-5">
                <div>
                    <div class="text-[10px] font-bold text-slate-600 uppercase">Jarak</div>
                    <div class="font-display font-black text-base text-slate-900" x-text="distanceKm.toFixed(2) + ' km'"></div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-600 uppercase">Waktu</div>
                    <div class="font-display font-black text-base text-slate-900" x-text="formattedDuration"></div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-600 uppercase">Avg Pace</div>
                    <div class="font-display font-black text-base text-blue-600" x-text="formattedAvgPace"></div>
                </div>
            </div>

            <!-- Save Form -->
            <form @submit.prevent="submitRun()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Aktivitas</label>
                        <input type="text"
                               x-model="runForm.title"
                               placeholder="Contoh: Lari Pagi GBK"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan / Rute</label>
                        <textarea x-model="runForm.notes"
                                  rows="2"
                                  placeholder="Bagaimana perasaan larimu hari ini? Cuaca, sepatu yang dipakai, atau rute..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs text-slate-900"></textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="button"
                            @click="showFinishModal = false"
                            class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-1.5">
                        <span x-show="!isSubmitting">Simpan Sesi</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function runTracker(userWeightKg) {
    return {
        // State
        state: 'idle', // 'idle' | 'running' | 'paused' | 'demo'
        isDemo: false,
        gpsLocked: false,
        gpsStatusMessage: 'Mencari sinyal GPS...',
        isCountingDown: false,
        countdownNumber: 3,
        showFinishModal: false,
        isSubmitting: false,
        userWeight: userWeightKg || 70,

        // Telemetry Data
        startTime: null,
        durationSeconds: 0,
        distanceKm: 0.0,
        currentPaceSeconds: 0,
        avgPaceSeconds: 0,
        bestPaceSeconds: 0,
        caloriesBurned: 0,
        routeCoordinates: [], // array of [lat, lng, timestamp]
        kmSplits: [], // array of { km: 1, duration_seconds: 320, pace_seconds: 320 }

        // Interval & Watcher IDs
        timerInterval: null,
        watchId: null,
        demoInterval: null,
        demoStepIndex: 0,

        // Map variables
        map: null,
        userMarker: null,
        polyline: null,
        lastPosition: null,
        lastSplitDistanceKm: 0,
        lastSplitTimeSeconds: 0,

        // Finish Form Data
        runForm: {
            title: '',
            notes: '',
        },

        // GBK Loop Coordinate Simulation Route for Demo
        gbkDemoRoute: [
            [-6.218571, 106.801849],
            [-6.218320, 106.802315],
            [-6.217890, 106.803010],
            [-6.217220, 106.803740],
            [-6.216310, 106.804290],
            [-6.215260, 106.804550],
            [-6.214150, 106.804470],
            [-6.213230, 106.804020],
            [-6.212450, 106.803320],
            [-6.211820, 106.802420],
            [-6.211510, 106.801450],
            [-6.211560, 106.800390],
            [-6.211930, 106.799430],
            [-6.212580, 106.798620],
            [-6.213450, 106.798030],
            [-6.214480, 106.797740],
            [-6.215570, 106.797770],
            [-6.216630, 106.798130],
            [-6.217520, 106.798820],
            [-6.218210, 106.799740],
            [-6.218571, 106.800800],
            [-6.218571, 106.801849]
        ],

        get statusLabel() {
            if (this.state === 'running') return 'Merekam GPS';
            if (this.state === 'paused') return 'Dijeda';
            if (this.state === 'demo') return 'Simulasi Berjalan';
            return 'Siap Lari';
        },

        get formattedDuration() {
            return this.formatSeconds(this.durationSeconds);
        },

        get formattedCurrentPace() {
            return this.formatPace(this.currentPaceSeconds);
        },

        get formattedAvgPace() {
            return this.formatPace(this.avgPaceSeconds);
        },

        get formattedBestPace() {
            return this.formatPace(this.bestPaceSeconds);
        },

        initTracker() {
            this.$nextTick(() => {
                this.initMap();
                this.detectInitialPosition();
            });
        },

        initMap() {
            if (this.map) return;

            // Default center: Jakarta GBK
            const defaultLat = -6.218571;
            const defaultLng = 106.801849;

            this.map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([defaultLat, defaultLng], 16);

            // Clean, high quality CartoDB Voyager map tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                subdomains: 'abcd'
            }).addTo(this.map);

            // Polyline styling: Strava bold blue with border
            this.polyline = L.polyline([], {
                color: '#2563EB',
                weight: 5,
                opacity: 0.95,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(this.map);

            // Custom pulsing user icon
            const userIcon = L.divIcon({
                className: 'custom-pulse-marker',
                html: `<div class="relative flex items-center justify-center w-6 h-6">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-600 border-2 border-white shadow-md"></span>
                </div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            this.userMarker = L.marker([defaultLat, defaultLng], { icon: userIcon }).addTo(this.map);
        },

        detectInitialPosition() {
            if (!navigator.geolocation) {
                this.gpsStatusMessage = 'GPS tidak didukung pada browser ini';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    this.gpsLocked = true;
                    this.gpsStatusMessage = 'Sinyal GPS Akurat';
                    if (this.map && this.userMarker) {
                        this.userMarker.setLatLng([lat, lng]);
                        this.map.setView([lat, lng], 17);
                    }
                },
                (err) => {
                    this.gpsLocked = false;
                    this.gpsStatusMessage = 'GPS tidak aktif. Kamu bisa menggunakan Mode Demo';
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        },

        centerMapOnUser() {
            if (this.userMarker && this.map) {
                this.map.panTo(this.userMarker.getLatLng());
            }
        },

        toggleDemoMode() {
            this.isDemo = !this.isDemo;
            if (this.isDemo) {
                this.gpsLocked = true;
                this.gpsStatusMessage = 'Mode Simulasi Demo Aktif';
                const firstCoord = this.gbkDemoRoute[0];
                if (this.map && this.userMarker) {
                    this.userMarker.setLatLng(firstCoord);
                    this.map.setView(firstCoord, 16);
                }
            } else {
                this.detectInitialPosition();
            }
        },

        startRun() {
            // Start countdown
            this.isCountingDown = true;
            this.countdownNumber = 3;

            const countInterval = setInterval(() => {
                this.countdownNumber--;
                if (this.countdownNumber <= 0) {
                    clearInterval(countInterval);
                    this.isCountingDown = false;
                    this.beginTracking();
                }
            }, 1000);
        },

        beginTracking() {
            this.state = this.isDemo ? 'demo' : 'running';
            this.startTime = new Date();

            // Setup timer loop (every 1 second)
            this.timerInterval = setInterval(() => {
                this.durationSeconds++;
                this.calculateTelemetry();
            }, 1000);

            if (this.isDemo) {
                this.startDemoLoop();
            } else {
                this.startGpsWatch();
            }
        },

        startGpsWatch() {
            if (!navigator.geolocation) return;

            this.watchId = navigator.geolocation.watchPosition(
                (position) => {
                    this.handleNewCoordinate(position.coords.latitude, position.coords.longitude, position.coords.accuracy);
                },
                (err) => {
                    console.warn('GPS error:', err);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 1000,
                    timeout: 5000
                }
            );
        },

        startDemoLoop() {
            this.demoStepIndex = 0;
            // GBK loop demo has ~22 waypoints. We interpolate smooth points between them.
            const route = this.gbkDemoRoute;
            this.lastPosition = { lat: route[0][0], lng: route[0][1] };
            this.routeCoordinates.push([route[0][0], route[0][1], Date.now()]);
            this.polyline.setLatLngs([[route[0][0], route[0][1]]]);

            let currWaypoint = 0;
            let subStep = 0;
            const subStepsPerWaypoint = 4; // Advance smooth distance

            this.demoInterval = setInterval(() => {
                if (this.state !== 'demo') return;

                const startWp = route[currWaypoint];
                const nextWp = route[(currWaypoint + 1) % route.length];

                subStep++;
                const progress = subStep / subStepsPerWaypoint;
                const lat = startWp[0] + (nextWp[0] - startWp[0]) * progress;
                const lng = startWp[1] + (nextWp[1] - startWp[1]) * progress;

                this.handleNewCoordinate(lat, lng, 5);

                if (subStep >= subStepsPerWaypoint) {
                    subStep = 0;
                    currWaypoint = (currWaypoint + 1) % route.length;
                }
            }, 1000);
        },

        handleNewCoordinate(lat, lng, accuracy) {
            if (this.state !== 'running' && this.state !== 'demo') return;

            const now = Date.now();
            const newCoord = [lat, lng, now];

            if (this.lastPosition) {
                const distMeters = this.calculateHaversineDistance(
                    this.lastPosition.lat, this.lastPosition.lng,
                    lat, lng
                );

                // Filter out small jitter (< 1.5m) and unrealistic GPS teleport jumps (> 50m in 1 sec)
                if (distMeters >= 1.5 && distMeters < 50) {
                    this.distanceKm += (distMeters / 1000);

                    // Instantaneous Pace calculation based on recent delta
                    const recentSeconds = 1;
                    const instantPaceSec = Math.round((recentSeconds / (distMeters / 1000)));
                    if (instantPaceSec > 180 && instantPaceSec < 1200) {
                        this.currentPaceSeconds = instantPaceSec;
                        if (this.bestPaceSeconds === 0 || instantPaceSec < this.bestPaceSeconds) {
                            this.bestPaceSeconds = instantPaceSec;
                        }
                    }

                    // Check for 1.0 KM Split trigger
                    this.checkKilometerSplit();
                }
            }

            this.lastPosition = { lat, lng };
            this.routeCoordinates.push(newCoord);

            // Update Leaflet map rendering
            if (this.userMarker) {
                this.userMarker.setLatLng([lat, lng]);
            }
            if (this.polyline) {
                this.polyline.addLatLng([lat, lng]);
            }
            if (this.map && (this.routeCoordinates.length % 3 === 0)) {
                this.map.panTo([lat, lng]);
            }
        },

        checkKilometerSplit() {
            const currentKmInteger = Math.floor(this.distanceKm);
            if (currentKmInteger > this.kmSplits.length && currentKmInteger >= 1) {
                const splitDuration = this.durationSeconds - this.lastSplitTimeSeconds;
                const splitPace = splitDuration; // since distance for this lap is exactly 1 km

                this.kmSplits.push({
                    km: currentKmInteger,
                    duration_seconds: splitDuration,
                    pace_seconds: splitPace
                });

                this.lastSplitTimeSeconds = this.durationSeconds;
                this.lastSplitDistanceKm = currentKmInteger;

                // Subtle audio feedback on split reached
                this.playSplitAudio();
            }
        },

        calculateTelemetry() {
            // Average Pace (total seconds / total km)
            if (this.distanceKm > 0.05) {
                this.avgPaceSeconds = Math.round(this.durationSeconds / this.distanceKm);
            }

            // Calorie estimation: ~ 1.036 kcal per kg body weight per km
            this.caloriesBurned = Math.round(this.userWeight * this.distanceKm * 1.036);
        },

        pauseRun() {
            this.state = 'paused';
            if (this.timerInterval) clearInterval(this.timerInterval);
            if (this.demoInterval) clearInterval(this.demoInterval);
            if (this.watchId) {
                navigator.geolocation.clearWatch(this.watchId);
                this.watchId = null;
            }
        },

        resumeRun() {
            this.state = this.isDemo ? 'demo' : 'running';
            this.timerInterval = setInterval(() => {
                this.durationSeconds++;
                this.calculateTelemetry();
            }, 1000);

            if (this.isDemo) {
                this.startDemoLoop();
            } else {
                this.startGpsWatch();
            }
        },

        openFinishModal() {
            this.pauseRun();

            // Default auto title
            const now = new Date();
            const hours = now.getHours();
            let period = 'Pagi';
            if (hours >= 11 && hours < 15) period = 'Siang';
            else if (hours >= 15 && hours < 18) period = 'Sore';
            else if (hours >= 18 || hours < 4) period = 'Malam';

            const day = now.getDate();
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            this.runForm.title = `Lari ${period} ${day} ${months[now.getMonth()]}`;

            this.showFinishModal = true;
        },

        async submitRun() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            const startedIso = this.startTime ? this.startTime.toISOString() : new Date(Date.now() - (this.durationSeconds * 1000)).toISOString();
            const endedIso = new Date().toISOString();

            const payload = {
                title: this.runForm.title,
                notes: this.runForm.notes,
                started_at: startedIso,
                ended_at: endedIso,
                duration_seconds: this.durationSeconds,
                distance_km: parseFloat(this.distanceKm.toFixed(3)),
                avg_pace_seconds: this.avgPaceSeconds,
                best_pace_seconds: this.bestPaceSeconds > 0 ? this.bestPaceSeconds : null,
                calories_burned: this.caloriesBurned,
                route_coordinates: JSON.stringify(this.routeCoordinates),
                km_splits: JSON.stringify(this.kmSplits)
            };

            try {
                const response = await fetch("{{ route('runs.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('Gagal menyimpan sesi lari. Silakan coba kembali.');
                    this.isSubmitting = false;
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menyimpan aktivitas lari.');
                this.isSubmitting = false;
            }
        },

        calculateHaversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Earth radius in meters
            const toRad = deg => deg * (Math.PI / 180);
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        },

        formatSeconds(sec) {
            if (!sec || isNaN(sec)) return '00:00';
            const h = Math.floor(sec / 3600);
            const m = Math.floor((sec % 3600) / 60);
            const s = Math.floor(sec % 60);

            if (h > 0) {
                return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        formatPace(sec) {
            if (!sec || isNaN(sec) || sec <= 0 || sec > 3600) return `--'--"`;
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return `${m}'${String(s).padStart(2, '0')}"`;
        },

        playSplitAudio() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime); // High pleasant A5 beep
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) {
                // Audio context may require previous interaction
            }
        }
    };
}
</script>
@endsection
