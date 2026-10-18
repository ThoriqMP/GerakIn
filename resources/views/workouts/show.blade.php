@extends('layouts.app', ['title' => $session->title])

@section('content')
<div class="space-y-6 pt-1 pb-16">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('workouts.history') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Riwayat
        </a>
        <a href="{{ route('workouts.summary', $session->id) }}" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors">
            Lihat Kartu Ringkasan
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-blue-600 font-semibold">{{ $session->started_at->isoFormat('dddd, D MMMM Y • HH:mm') }}</span>
                <h2 class="font-display font-black text-2xl text-slate-900 mt-0.5">{{ $session->title }}</h2>
                @if ($session->notes)
                    <p class="text-xs text-slate-600 mt-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        "{{ $session->notes }}"
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="text-center p-3 rounded-2xl bg-slate-50 border border-slate-200 min-w-[90px]">
                    <div class="text-[10px] uppercase font-bold text-slate-600">Durasi</div>
                    <div class="font-display font-bold text-lg text-slate-900">{{ round($session->duration_seconds / 60) }} mnt</div>
                </div>
                <div class="text-center p-3 rounded-2xl bg-blue-50 border border-blue-100 min-w-[90px]">
                    <div class="text-[10px] uppercase font-bold text-blue-700">Volume</div>
                    <div class="font-display font-bold text-lg text-blue-700">{{ number_format($session->total_volume_kg) }} kg</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exercise Sets Breakdown -->
    <div class="space-y-4">
        @foreach ($groupedSets as $exerciseId => $sets)
            @php $ex = $sets->first()->exercise; @endphp
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="font-display font-bold text-base text-slate-900">{{ $ex->name }}</h3>
                        <span class="text-[10px] text-slate-600 uppercase capitalize">{{ $ex->primary_muscle }} • {{ $ex->equipment }}</span>
                    </div>
                    <span class="text-xs font-semibold text-slate-600">{{ $sets->where('is_completed', true)->count() }} sets</span>
                </div>

                <div class="space-y-1.5 text-xs">
                    @foreach ($sets as $set)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-display font-bold text-slate-600 w-5 text-center">{{ $set->set_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $set->set_type === 'warmup' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $set->set_type }}
                                </span>
                                @if ($set->weight_kg > 0)
                                    <span class="font-bold text-slate-900">{{ $set->weight_kg }} kg × {{ $set->reps }} reps</span>
                                @else
                                    <span class="font-bold text-slate-900">{{ $set->reps }} reps <span class="text-xs text-blue-600 font-semibold">(Bodyweight)</span></span>
                                @endif
                                @if ($set->rpe)
                                    <span class="text-slate-600 text-[11px]">(RPE {{ $set->rpe }})</span>
                                @endif
                            </div>
                            <div>
                                @if ($set->is_pr)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold uppercase">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        <span>PR</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
