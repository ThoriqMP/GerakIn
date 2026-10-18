@extends('layouts.app', ['title' => 'Sesi Latihan Aktif'])

@section('content')
@php
    $hasStarted = $session->isTimerActive();
    $initialTimerStartedAt = $session->timer_started_at ? $session->timer_started_at->toIso8601String() : null;
@endphp

<div x-data="{
    // Workout State: Planning vs Active
    isTimerRunning: {{ $hasStarted ? 'true' : 'false' }},
    isPlanning: {{ $hasStarted ? 'false' : 'true' }},
    timerStartedAt: {{ $initialTimerStartedAt ? "'$initialTimerStartedAt'" : 'null' }},
    
    // Elapsed workout timer
    elapsedSeconds: 0,
    elapsedFormatted: '00:00',
    workoutInterval: null,

    // Rest Timer
    restTimerActive: false,
    restDuration: {{ $defaultRest }},
    restRemaining: {{ $defaultRest }},
    restInterval: null,

    // Modals
    showAddExerciseModal: false,
    showFinishModal: false,
    exerciseSearch: '',
    exerciseCategory: 'all',

    init() {
        if (this.isTimerRunning && this.timerStartedAt) {
            this.startElapsedTimer();
        } else {
            this.elapsedSeconds = 0;
            this.elapsedFormatted = '00:00';
        }
    },

    startWorkout() {
        if (!this.isTimerRunning) {
            this.isTimerRunning = true;
            this.isPlanning = false;
            if (!this.timerStartedAt) {
                this.timerStartedAt = new Date().toISOString();
            }
            this.startElapsedTimer();

            // Notify server that workout timer has started
            const csrfToken = document.querySelector('meta[name=\'csrf-token\']').getAttribute('content');
            fetch('{{ route('workouts.start-timer', $session->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).catch(e => console.warn('Could not sync timer start to server:', e));
        }
    },

    togglePause() {
        if (this.isPlanning) {
            this.startWorkout();
            return;
        }

        if (this.isTimerRunning) {
            this.isTimerRunning = false;
            if (this.workoutInterval) clearInterval(this.workoutInterval);
        } else {
            this.isTimerRunning = true;
            this.workoutInterval = setInterval(() => {
                this.elapsedSeconds++;
                this.formatElapsed();
            }, 1000);
        }
    },

    startElapsedTimer() {
        if (this.workoutInterval) clearInterval(this.workoutInterval);

        if (this.timerStartedAt) {
            const startMs = new Date(this.timerStartedAt).getTime();
            this.elapsedSeconds = Math.max(0, Math.floor((Date.now() - startMs) / 1000));
        }
        this.formatElapsed();

        this.workoutInterval = setInterval(() => {
            if (this.isTimerRunning) {
                this.elapsedSeconds++;
                this.formatElapsed();
            }
        }, 1000);
    },

    formatElapsed() {
        const mins = Math.floor(this.elapsedSeconds / 60);
        const secs = this.elapsedSeconds % 60;
        this.elapsedFormatted = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    },

    startRestTimer(customSeconds = null) {
        if (this.restInterval) clearInterval(this.restInterval);
        const duration = customSeconds || this.restDuration;
        this.restRemaining = duration;
        this.restTimerActive = true;

        this.restInterval = setInterval(() => {
            if (this.restRemaining > 0) {
                this.restRemaining--;
            } else {
                this.stopRestTimer();
                window.playTimerChime();
            }
        }, 1000);
    },

    stopRestTimer() {
        if (this.restInterval) clearInterval(this.restInterval);
        this.restInterval = null;
        this.restTimerActive = false;
    },

    addRestTime(sec) {
        this.restRemaining = Math.max(0, this.restRemaining + sec);
    },

    // Save set via AJAX
    async updateSetData({ setId, weight, reps, completed }) {
        if (completed && this.isPlanning) {
            this.startWorkout();
        }

        const rpeInput = document.getElementById('rpe_' + setId);
        const setTypeInput = document.getElementById('type_' + setId);

        const payload = {
            weight_kg: parseFloat(weight) || 0,
            reps: parseInt(reps) || 0,
            rpe: rpeInput ? parseInt(rpeInput.value) || 7 : 7,
            set_type: setTypeInput ? setTypeInput.value : 'normal',
        };
        if (typeof completed === 'boolean') {
            payload.is_completed = completed;
        }

        try {
            const csrfToken = document.querySelector('meta[name=\'csrf-token\']').getAttribute('content');
            const res = await fetch('/workout-sets/' + setId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                const data = await res.json();
                if (data.timer_started_at && !this.timerStartedAt) {
                    this.timerStartedAt = data.timer_started_at;
                }
                if (completed) {
                    if (data.is_pr) {
                        window.confetti({ particleCount: 80, spread: 70, origin: { y: 0.6 } });
                    }
                    this.startRestTimer();
                }
            }
        } catch (e) {
            console.warn('Set update sync error:', e);
            if (completed) this.startRestTimer();
        }
    },

    toggleSet(setId, isCompleted) {
        const weightInput = document.getElementById('weight_' + setId);
        const repsInput = document.getElementById('reps_' + setId);
        this.updateSetData({
            setId: setId,
            weight: weightInput ? parseFloat(weightInput.value) || 0 : 0,
            reps: repsInput ? parseInt(repsInput.value) || 0 : 0,
            completed: isCompleted
        });
    }
}" @save-set-data.window="updateSetData($event.detail)" class="space-y-5 pt-1 pb-16">

    <!-- Top Sticky Header for Active Workout / Planning -->
    <div class="bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 sticky top-16 z-30">
        <div class="flex items-center gap-3">
            <div :class="isPlanning ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-blue-50 text-blue-600 border-blue-200'"
                 class="w-10 h-10 rounded-2xl border flex items-center justify-center font-display font-black transition-colors">
                <template x-if="isPlanning">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </template>
                <template x-if="!isPlanning">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </template>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span x-show="isPlanning" class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">Mode Perencanaan</span>
                    <span x-show="!isPlanning && isTimerRunning" class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>Sesi Latihan Berlangsung</span>
                    </span>
                    <span x-show="!isPlanning && !isTimerRunning" class="text-[10px] font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                        Sesi Dijeda
                    </span>
                </div>
                <h2 class="font-display font-black text-base sm:text-lg text-slate-900 truncate max-w-[220px] sm:max-w-md mt-0.5">
                    {{ $session->title }}
                </h2>
            </div>
        </div>

        <div class="flex items-center flex-wrap justify-between sm:justify-end gap-2 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
            <!-- Live Timer Pill -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all"
                 :class="isTimerRunning ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-slate-100 border-slate-200 text-slate-600'">
                <svg class="w-4 h-4" :class="isTimerRunning ? 'text-blue-600 animate-spin' : 'text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-display font-bold text-sm tracking-wider" x-text="elapsedFormatted">00:00</span>
            </div>

            <!-- Start / Pause / Resume Button -->
            <template x-if="isPlanning">
                <button type="button" @click="startWorkout()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-sm active:scale-95 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span>Mulai Latihan</span>
                </button>
            </template>

            <template x-if="!isPlanning">
                <button type="button" @click="togglePause()" class="px-3 py-2 rounded-xl border text-xs sm:text-sm font-bold shadow-xs active:scale-95 transition-all flex items-center gap-1.5"
                        :class="isTimerRunning ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200' : 'bg-blue-600 hover:bg-blue-700 text-white border-blue-600'">
                    <template x-if="isTimerRunning">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                            <span>Jeda</span>
                        </span>
                    </template>
                    <template x-if="!isTimerRunning">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span>Lanjutkan</span>
                        </span>
                    </template>
                </button>
            </template>

            <!-- Finish Workout CTA -->
            <button type="button" @click="showFinishModal = true" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm active:scale-95 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Selesai</span>
            </button>

            <!-- Discard Button -->
            <form method="POST" action="{{ route('workouts.discard', $session->id) }}" onsubmit="return confirm('Apakah kamu yakin ingin membatalkan dan menghapus sesi latihan ini?')">
                @csrf
                <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 transition-colors" title="Batalkan Latihan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Planning Notice Banner (When in planning mode) -->
    <div x-show="isPlanning" x-transition
         class="bg-blue-50/80 rounded-2xl p-3.5 sm:p-4 border border-blue-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-start sm:items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 font-bold">
                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-900">Perencanaan Gerakan & Target Hari Ini</span>
                <p class="text-[11px] text-slate-600">Susun daftar latihan, sesuaikan target beban & repetisi tanpa terburu-buru. Waktu latihan baru akan berjalan saat kamu menekan tombol <strong>Mulai Latihan</strong>.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
            <button type="button" @click="showAddExerciseModal = true" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-blue-700 font-bold text-xs border border-blue-200 shadow-xs transition-colors flex items-center gap-1">
                <span>+ Gerakan</span>
            </button>
            <button type="button" @click="startWorkout()" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <span>Mulai Sekarang</span>
            </button>
        </div>
    </div>

    <!-- BUILT-IN REST TIMER BAR -->
    <div x-show="restTimerActive" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-blue-50/90 rounded-3xl p-4 sm:p-5 border border-blue-200 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center font-display font-black">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                        <span>Waktu Istirahat (Rest Timer)</span>
                    </div>
                    <div class="font-display font-black text-2xl sm:text-3xl text-slate-900">
                        <span x-text="String(Math.floor(restRemaining / 60)).padStart(2, '0')"></span>:<span x-text="String(restRemaining % 60).padStart(2, '0')"></span>
                    </div>
                </div>
            </div>

            <!-- Quick Controls: -15s, +15s, Skip -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" @click="addRestTime(-15)" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                    -15s
                </button>
                <button type="button" @click="addRestTime(15)" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                    +15s
                </button>
                <button type="button" @click="stopRestTimer()" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                    Lewati
                </button>
            </div>
        </div>
    </div>

    <!-- EXERCISES LIST -->
    <div class="space-y-4">
        @forelse ($groupedSets as $exerciseId => $sets)
            @php
                $exercise = $exerciseReferences[$exerciseId]['exercise'] ?? null;
                $pr = $exerciseReferences[$exerciseId]['pr'] ?? null;
                $prevSets = $exerciseReferences[$exerciseId]['previous_sets'] ?? [];
            @endphp

            @if ($exercise)
                @php
                    $isBodyweight = $exercise->isBodyweight();
                    $hasAddedWeight = $sets->contains(fn($s) => (float)$s->weight_kg > 0);
                @endphp
                <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-sm" x-data="{
                    setsCount: {{ $sets->count() }},
                    isBodyweight: {{ $isBodyweight ? 'true' : 'false' }},
                    showWeighted: {{ ($isBodyweight && !$hasAddedWeight) ? 'false' : 'true' }}
                }">
                    <!-- Exercise Header -->
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ ucfirst($exercise->primary_muscle) }}
                                </span>
                                <span class="text-[10px] uppercase font-semibold text-slate-600">
                                    {{ ucfirst($exercise->equipment) }}
                                </span>
                                @if ($isBodyweight)
                                    <button type="button"
                                            @click="showWeighted = !showWeighted; if (!showWeighted) { $dispatch('reset-weights-{{ $exercise->id }}'); }"
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-md border transition-all inline-flex items-center gap-1 active:scale-95"
                                            :class="showWeighted ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border-slate-200'">
                                        <template x-if="!showWeighted">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Tambah Beban (Weighted)</span>
                                            </span>
                                        </template>
                                        <template x-if="showWeighted">
                                            <span class="flex items-center gap-1">
                                                <span>Mode Beban Tubuh Murni</span>
                                                <span class="text-xs font-bold">&times;</span>
                                            </span>
                                        </template>
                                    </button>
                                @endif
                            </div>
                            <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">
                                {{ $exercise->name }}
                            </h3>
                        </div>

                        <!-- PR Ghost Text / Badge -->
                        <div class="text-right">
                            @if ($pr)
                                <div class="px-2.5 py-1 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                    @if ($pr->weight_kg > 0)
                                        <span>PR: {{ $pr->weight_kg }}kg × {{ $pr->reps }}</span>
                                    @else
                                        <span>PR: {{ $pr->reps }} Reps (Bodyweight)</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] text-slate-600">Belum ada PR</span>
                            @endif
                        </div>
                    </div>

                    <!-- Sets Table -->
                    <div class="overflow-x-auto -mx-2 sm:mx-0">
                        <table class="w-full text-left text-xs min-w-[320px]">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-wider text-slate-600 border-b border-slate-100">
                                    <th class="py-2 px-1 text-center w-8">Set</th>
                                    <th class="py-2 px-1">Tipe</th>
                                    <th x-show="showWeighted" class="py-2 px-1 text-center">Beban (kg)</th>
                                    <th class="py-2 px-1 text-center">Reps</th>
                                    <th class="py-2 px-1 text-center">RPE</th>
                                    <th class="py-2 px-2 text-center w-12">Selesai</th>
                                    <th class="py-2 px-1 text-center w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($sets as $idx => $set)
                                    @php
                                        $prevSet = $prevSets[$idx] ?? null;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors" x-data="{
                                        weight: {{ $set->weight_kg }},
                                        reps: {{ $set->reps }},
                                        completed: {{ $set->is_completed ? 'true' : 'false' }},
                                        saveTimer: null,
                                        queueSave(isCompletionToggle = null) {
                                            clearTimeout(this.saveTimer);
                                            const comp = isCompletionToggle !== null ? isCompletionToggle : this.completed;
                                            this.saveTimer = setTimeout(() => {
                                                $dispatch('save-set-data', {
                                                    setId: {{ $set->id }},
                                                    weight: showWeighted ? this.weight : 0,
                                                    reps: this.reps,
                                                    completed: comp
                                                });
                                            }, isCompletionToggle !== null ? 0 : 300);
                                        },
                                        stepWeight(delta) {
                                            this.weight = Math.max(0, +(this.weight + delta).toFixed(1));
                                            const el = document.getElementById('weight_{{ $set->id }}');
                                            if (el) el.value = this.weight;
                                            this.queueSave();
                                        },
                                        stepReps(delta) {
                                            this.reps = Math.max(0, this.reps + delta);
                                            const el = document.getElementById('reps_{{ $set->id }}');
                                            if (el) el.value = this.reps;
                                            this.queueSave();
                                        }
                                    }" @reset-weights-{{ $exercise->id }}.window="weight = 0; queueSave()">
                                        <!-- Set Number -->
                                        <td class="py-3 px-1 text-center font-display font-extrabold text-xs text-slate-600">
                                            {{ $set->set_number }}
                                        </td>

                                        <!-- Set Type -->
                                        <td class="py-3 px-1">
                                            <select id="type_{{ $set->id }}" @change="queueSave()" class="py-1 px-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-semibold focus:outline-none focus:border-blue-500">
                                                <option value="normal" {{ $set->set_type === 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="warmup" {{ $set->set_type === 'warmup' ? 'selected' : '' }}>Warmup</option>
                                                <option value="drop" {{ $set->set_type === 'drop' ? 'selected' : '' }}>Drop</option>
                                                <option value="failure" {{ $set->set_type === 'failure' ? 'selected' : '' }}>Failure</option>
                                            </select>
                                        </td>

                                        <!-- Weight Stepper Input (Shown only if showWeighted is true) -->
                                        <td x-show="showWeighted" class="py-3 px-1 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="stepWeight(-2.5)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold active:scale-90 transition-transform">-</button>
                                                <div class="relative w-16">
                                                    <input type="number" step="0.5" id="weight_{{ $set->id }}" x-model.number="weight" @input="queueSave()"
                                                           class="w-full text-center py-1 px-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-sm focus:outline-none focus:border-blue-500">
                                                    @if ($prevSet && $prevSet['weight_kg'] > 0)
                                                        <span class="block text-[9px] text-slate-600 font-normal leading-none mt-0.5" title="Sesi lalu">
                                                            Lalu: {{ $prevSet['weight_kg'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <button type="button" @click="stepWeight(2.5)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold active:scale-90 transition-transform">+</button>
                                            </div>
                                        </td>

                                        <!-- Reps Stepper Input -->
                                        <td class="py-3 px-1 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="stepReps(-1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold active:scale-90 transition-transform">-</button>
                                                <div class="relative w-12">
                                                    <input type="number" id="reps_{{ $set->id }}" x-model.number="reps" @input="queueSave()"
                                                           class="w-full text-center py-1 px-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-sm focus:outline-none focus:border-blue-500">
                                                    @if ($prevSet)
                                                        <span class="block text-[9px] text-slate-600 font-normal leading-none mt-0.5" title="Sesi lalu">
                                                            Lalu: {{ $prevSet['reps'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <button type="button" @click="stepReps(1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold active:scale-90 transition-transform">+</button>
                                            </div>
                                        </td>

                                        <!-- RPE -->
                                        <td class="py-3 px-1 text-center">
                                            <select id="rpe_{{ $set->id }}" @change="queueSave()" class="py-1 px-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-semibold focus:outline-none focus:border-blue-500">
                                                <option value="6" {{ ($set->rpe ?? 8) == 6 ? 'selected' : '' }}>RPE 6</option>
                                                <option value="7" {{ ($set->rpe ?? 8) == 7 ? 'selected' : '' }}>RPE 7</option>
                                                <option value="8" {{ ($set->rpe ?? 8) == 8 ? 'selected' : '' }}>RPE 8</option>
                                                <option value="9" {{ ($set->rpe ?? 8) == 9 ? 'selected' : '' }}>RPE 9</option>
                                                <option value="10" {{ ($set->rpe ?? 8) == 10 ? 'selected' : '' }}>RPE 10</option>
                                            </select>
                                        </td>

                                        <!-- Completion Checkbox -->
                                        <td class="py-3 px-2 text-center">
                                            <button type="button"
                                                    @click="completed = !completed; queueSave(completed)"
                                                    :class="completed ? 'set-checkbox completed' : 'set-checkbox uncompleted'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </td>

                                        <!-- Delete Set -->
                                        <td class="py-3 px-1 text-center">
                                            <a href="{{ route('workout-sets.delete.get', $set->id) }}" onclick="return confirm('Hapus set ini?')" class="text-slate-600 hover:text-rose-600 p-1">
                                                &times;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Set Form for this exercise -->
                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end">
                        <form method="POST" action="{{ route('workouts.add-set', $session->id) }}">
                            @csrf
                            <input type="hidden" name="exercise_id" value="{{ $exercise->id }}">
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-1.5 border border-slate-200 transition-colors">
                                <span>+ Tambah Set</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-base">Belum Ada Gerakan di Sesi Ini</h4>
                <p class="text-xs text-slate-600 mt-1 mb-4">Pilih dan tambahkan gerakan dari perpustakaan latihan GerakIn.</p>
                <button type="button" @click="showAddExerciseModal = true" class="px-6 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                    + Tambah Gerakan Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <!-- ADD EXERCISE BUTTON CTA -->
    <div class="pt-1">
        <button type="button" @click="showAddExerciseModal = true" class="w-full py-3.5 rounded-3xl bg-white hover:bg-blue-50 border-2 border-dashed border-slate-200 hover:border-blue-400 text-slate-700 hover:text-blue-700 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
            <span class="w-5 h-5 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">+</span>
            <span>Tambah Gerakan Olahraga Lainnya</span>
        </button>
    </div>

    <!-- MODAL: ADD EXERCISE -->
    <div x-show="showAddExerciseModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full max-h-[85vh] flex flex-col shadow-xl overflow-hidden relative"
             @click.away="showAddExerciseModal = false">
            
            <!-- FIXED HEADER: Title, Search, and Category Filter Pills -->
            <div class="p-4 sm:p-5 pb-3 border-b border-slate-200 shrink-0 bg-white space-y-2.5 z-10 shadow-xs">
                <!-- Title & Close -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="font-display font-bold text-base text-slate-900">Pilih Gerakan Olahraga</h3>
                    </div>
                    <button type="button" @click="showAddExerciseModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none p-1 transition-colors">&times;</button>
                </div>

                <!-- Search Bar -->
                <div>
                    <input type="text" x-model="exerciseSearch" placeholder="Cari nama gerakan (misal: Bench Press, Squat)..."
                           class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-600 text-xs focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                <!-- Muscle Category Filter Pills (Fixed, Non-Scrollable, Flex-Wrap) -->
                <div>
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <template x-for="cat in [
                            { key: 'all', label: 'Semua Gerakan' },
                            { key: 'home', label: 'Home / Bodyweight' },
                            { key: 'chest', label: 'Dada' },
                            { key: 'back', label: 'Punggung' },
                            { key: 'legs', label: 'Kaki' },
                            { key: 'shoulders', label: 'Bahu' },
                            { key: 'arms', label: 'Lengan' },
                            { key: 'core', label: 'Perut / Core' },
                            { key: 'cardio', label: 'Kardio' },
                        ]" :key="cat.key">
                            <button type="button" @click="exerciseCategory = cat.key"
                                    :class="exerciseCategory === cat.key ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/70'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition-all" x-text="cat.label">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- SCROLLABLE EXERCISE LIST -->
            <div class="overflow-y-auto flex-1 min-h-0 divide-y divide-slate-100 px-4 sm:px-5 py-1">
                @foreach ($allExercises as $ex)
                    <form method="POST" action="{{ route('workouts.add-exercise', $session->id) }}"
                          x-show="('{{ strtolower($ex->name) }}'.includes(exerciseSearch.toLowerCase())) && (exerciseCategory === 'all' || (exerciseCategory === 'home' ? '{{ $ex->equipment }}' === 'bodyweight' : exerciseCategory === '{{ $ex->primary_muscle }}'))"
                          class="py-3 flex items-center justify-between hover:bg-slate-50 px-2 rounded-xl transition-colors">
                        @csrf
                        <input type="hidden" name="exercise_id" value="{{ $ex->id }}">
                        <div>
                            <div class="font-display font-bold text-sm text-slate-900">{{ $ex->name }}</div>
                            <div class="text-[10px] text-slate-600 capitalize">{{ $ex->primary_muscle }} • {{ $ex->equipment }}</div>
                        </div>
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 text-xs font-bold border border-blue-200 transition-all">
                            + Pilih
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL: FINISH WORKOUT -->
    <div x-show="showFinishModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 max-w-md w-full shadow-xl relative"
             @click.away="showFinishModal = false"
             x-data="{ mood: 5 }">
            <div class="text-center mb-4">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-display font-bold text-lg text-slate-900">Selesaikan Latihan?</h3>
                <p class="text-xs text-slate-600 mt-1">
                    Durasi: <span class="font-bold text-blue-600" x-text="elapsedFormatted"></span>. Sesi siap disimpan ke linimasa!
                </p>
            </div>

            <form method="POST" action="{{ route('workouts.finish', $session->id) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="mood_rating" :value="mood">
                <input type="hidden" name="duration_seconds" :value="elapsedSeconds">

                <!-- Mood Rating (1-5) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 text-center">Tingkat Kepuasan & Intensitas Sesi</label>
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" @click="mood = 1" :class="mood === 1 ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-10 h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all">1</button>
                        <button type="button" @click="mood = 2" :class="mood === 2 ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-10 h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all">2</button>
                        <button type="button" @click="mood = 3" :class="mood === 3 ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-10 h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all">3</button>
                        <button type="button" @click="mood = 4" :class="mood === 4 ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-10 h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all">4</button>
                        <button type="button" @click="mood = 5" :class="mood === 5 ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-10 h-10 rounded-xl text-sm font-bold flex items-center justify-center transition-all">5</button>
                    </div>
                    <p class="text-[11px] text-center text-slate-500 font-medium mt-1.5" x-text="['', '1 - Sangat Berat / Melelahkan', '2 - Berat', '3 - Cukup Baik', '4 - Bagus & Bertenaga', '5 - Sangat Optimal & Puas'][mood]"></p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Sesi Latihan</label>
                    <textarea name="notes" rows="2" placeholder="Tuliskan evaluasi latihan atau rekor yang dicapai..."
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-600 text-xs focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showFinishModal = false" class="w-1/3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-sm">
                        Simpan & Lihat Ringkasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
