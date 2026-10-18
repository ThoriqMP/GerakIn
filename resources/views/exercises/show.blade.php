@extends('layouts.app', ['title' => $exercise->name])

@section('content')
<div class="space-y-6 pt-1 pb-16">
    <div class="flex items-center justify-between">
        <a href="{{ route('exercises.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Daftar Gerakan
        </a>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="text-[10px] uppercase font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                        {{ $exercise->primary_muscle }}
                    </span>
                    <span class="text-xs font-semibold text-slate-600 capitalize">
                        Alat: {{ $exercise->equipment }}
                    </span>
                    @if ($exercise->secondary_muscle)
                        <span class="text-[11px] text-slate-600">
                            • Otot Sekunder: {{ $exercise->secondary_muscle }}
                        </span>
                    @endif
                </div>

                <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900">{{ $exercise->name }}</h2>
            </div>

            <!-- PR Badge Card -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-right min-w-[160px]">
                <div class="text-[10px] uppercase font-bold tracking-wider text-amber-800 flex items-center justify-end gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>Rekor Pribadi (PR)</span>
                </div>
                @if ($pr)
                    @if ($pr->weight_kg > 0)
                        <div class="font-display font-black text-2xl text-slate-900 mt-1">
                            {{ $pr->weight_kg }} <span class="text-xs font-bold text-slate-600">kg</span>
                        </div>
                        <div class="text-xs font-semibold text-amber-700">
                            {{ $pr->reps }} Reps • e1RM: {{ $pr->estimated_one_rep_max }} kg
                        </div>
                    @else
                        <div class="font-display font-black text-2xl text-slate-900 mt-1">
                            {{ $pr->reps }} <span class="text-xs font-bold text-slate-600">Reps</span>
                        </div>
                        <div class="text-xs font-semibold text-amber-700">
                            Bodyweight (Beban Tubuh)
                        </div>
                    @endif
                @else
                    <div class="text-xs text-slate-600 mt-1">Belum ada rekor tercatat</div>
                @endif
            </div>
        </div>

        @if ($exercise->instructions)
            <div class="mt-6 pt-5 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Petunjuk & Teknik Gerakan</h4>
                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    {{ $exercise->instructions }}
                </p>
            </div>
        @endif
    </div>

    <!-- History of Sets for this Exercise -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <h3 class="font-display font-black text-lg text-slate-900 mb-3">Riwayat Angkatan Kamu</h3>

        @if ($historySets->isEmpty())
            <p class="text-xs text-slate-600">Kamu belum pernah mencatat set untuk gerakan ini. Masukkan gerakan ini ke sesi latihanmu!</p>
        @else
            <div class="space-y-2">
                @foreach ($historySets as $set)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <span class="text-slate-600">{{ $set->workoutSession?->started_at?->format('d M Y') }}</span>
                            @if ($set->weight_kg > 0)
                                <span class="font-bold text-slate-900">{{ $set->weight_kg }} kg × {{ $set->reps }} reps</span>
                            @else
                                <span class="font-bold text-slate-900">{{ $set->reps }} reps <span class="text-blue-600 font-semibold">(Beban Tubuh)</span></span>
                            @endif
                            @if ($set->rpe)
                                <span class="text-slate-600 text-[11px]">(RPE {{ $set->rpe }})</span>
                            @endif
                        </div>
                        <div>
                            @if ($set->is_pr)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold uppercase">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    <span>Rekor PR</span>
                                </span>
                            @elseif ($set->weight_kg > 0)
                                <span class="text-slate-600 text-[11px]">e1RM: {{ $set->estimated_one_rep_max }} kg</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
