@extends('layouts.app', ['title' => 'Buat Template Routine'])

@section('content')
<div class="space-y-6 pt-1 pb-16" x-data="{
    name: '{{ old('name', '') }}',
    description: '{{ old('description', '') }}',
    color_tag: '{{ old('color_tag', '#2563EB') }}',
    exercisesList: [
        { exercise_id: {{ $exercises->first()->id ?? 1 }}, target_sets: 3, target_reps: 10, rest_seconds: 90 }
    ],
    addExerciseRow() {
        this.exercisesList.push({
            exercise_id: {{ $exercises->first()->id ?? 1 }},
            target_sets: 3,
            target_reps: 10,
            rest_seconds: 90
        });
    },
    removeExerciseRow(index) {
        if (this.exercisesList.length > 1) {
            this.exercisesList.splice(index, 1);
        }
    }
}">
    <div class="flex items-center justify-between">
        <a href="{{ route('routines.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Daftar Jadwal
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="mb-6">
            <h2 class="font-display font-black text-2xl text-slate-900">Buat Template Jadwal Baru</h2>
            <p class="text-xs text-slate-600 mt-1">Susun gerakan, target set, repetisi, dan timer istirahat.</p>
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

        <form method="POST" action="{{ route('routines.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Template Latihan</label>
                    <input type="text" name="name" x-model="name" required placeholder="Contoh: Push Day (Dada & Bahu)"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-display font-bold text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Warna Tag</label>
                    <div class="flex items-center gap-2 pt-1">
                        <template x-for="c in ['#2563EB', '#0284C7', '#0D9488', '#16A34A', '#D97706', '#9333EA']" :key="c">
                            <button type="button" @click="color_tag = c"
                                    :class="color_tag === c ? 'scale-110 ring-2 ring-blue-500 shadow-sm' : 'opacity-70 hover:opacity-100'"
                                    class="w-7 h-7 rounded-full transition-all" :style="'background-color: ' + c">
                            </button>
                        </template>
                        <input type="hidden" name="color_tag" :value="color_tag">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat (Opsional)</label>
                <textarea name="description" x-model="description" rows="2" placeholder="Fokus pada hypertrophy dada dan trisep..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <!-- Exercises In Routine -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-display font-bold text-base text-slate-900">Daftar Gerakan</h3>
                    <button type="button" @click="addExerciseRow()" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 flex items-center gap-1 transition-all">
                        <span>+ Tambah Gerakan</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(row, index) in exercisesList" :key="index">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="flex items-center justify-between sm:justify-start gap-2">
                                <span class="font-display font-bold text-slate-600 text-sm w-6 text-center" x-text="index + 1"></span>
                                <div class="flex-1 sm:hidden">
                                    <span class="text-xs font-bold text-slate-700">Gerakan #<span x-text="index + 1"></span></span>
                                </div>
                                <button type="button" @click="removeExerciseRow(index)" class="sm:hidden p-1.5 text-slate-500 hover:text-rose-600 rounded-lg" title="Hapus gerakan ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <!-- Exercise Selector -->
                            <div class="flex-grow w-full sm:w-auto min-w-0">
                                <select :name="'exercises[' + index + '][exercise_id]'" x-model.number="row.exercise_id"
                                        class="w-full py-2 px-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-semibold focus:outline-none focus:border-blue-500">
                                    @foreach ($exercises as $ex)
                                        <option value="{{ $ex->id }}">
                                            {{ $ex->name }} ({{ ucfirst($ex->primary_muscle) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Inputs Row: Sets, Reps, Rest -->
                            <div class="grid grid-cols-3 gap-2 w-full sm:w-auto sm:flex sm:items-center">
                                <!-- Target Sets -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                    <span class="text-[10px] uppercase font-bold text-slate-600">Sets</span>
                                    <input type="number" :name="'exercises[' + index + '][target_sets]'" x-model.number="row.target_sets" min="1" max="15"
                                           class="w-full sm:w-14 text-center py-1.5 px-1 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-bold">
                                </div>

                                <!-- Target Reps -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                    <span class="text-[10px] uppercase font-bold text-slate-600">Reps</span>
                                    <input type="number" :name="'exercises[' + index + '][target_reps]'" x-model.number="row.target_reps" min="1" max="100"
                                           class="w-full sm:w-14 text-center py-1.5 px-1 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-bold">
                                </div>

                                <!-- Rest Seconds -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                    <span class="text-[10px] uppercase font-bold text-slate-600">Rest</span>
                                    <select :name="'exercises[' + index + '][rest_seconds]'" x-model.number="row.rest_seconds"
                                            class="w-full sm:w-auto py-1.5 px-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-semibold">
                                        <option value="45">45s</option>
                                        <option value="60">60s</option>
                                        <option value="90">90s</option>
                                        <option value="120">120s</option>
                                        <option value="180">180s</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Desktop Remove Row -->
                            <div class="hidden sm:block shrink-0">
                                <button type="button" @click="removeExerciseRow(index)" class="p-1.5 text-slate-500 hover:text-rose-600 rounded-lg" title="Hapus gerakan ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('routines.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm active:scale-95 transition-all">
                    Simpan Template Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
