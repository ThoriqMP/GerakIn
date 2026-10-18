@extends('layouts.app', ['title' => 'Riwayat Latihan'])

@section('content')
<div class="space-y-6 pt-1 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-display font-black text-2xl text-slate-900">Riwayat Latihan</h2>
            <p class="text-xs text-slate-600 mt-0.5">Semua sesi olahraga yang telah kamu selesaikan.</p>
        </div>
        <a href="{{ route('workouts.start') }}" class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-transform active:scale-95 self-start sm:self-auto">
            + Latihan Baru
        </a>
    </div>

    @if ($workouts->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="font-bold text-slate-900 text-base">Belum Ada Riwayat Sesi</h3>
            <p class="text-xs text-slate-600 mt-1 mb-4">Mulailah latihan pertamamu hari ini!</p>
            <a href="{{ route('workouts.start') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-sm">
                Mulai Sekarang
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($workouts as $session)
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm hover:border-slate-300 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-xs text-slate-600">{{ $session->started_at->isoFormat('dddd, D MMMM Y • HH:mm') }}</span>
                                @if ($session->sets->where('is_pr', true)->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        <span>{{ $session->sets->where('is_pr', true)->count() }} PR</span>
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-display font-bold text-base sm:text-lg text-slate-900 truncate">
                                {{ $session->title }}
                            </h3>
                            <div class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-2 sm:gap-3">
                                <span>Durasi: {{ round($session->duration_seconds / 60) }} mnt</span>
                                <span>•</span>
                                <span>Volume: <span class="text-blue-600 font-bold">{{ number_format($session->total_volume_kg) }} kg</span></span>
                                <span>•</span>
                                <span>{{ $session->sets->where('is_completed', true)->count() }} set</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 justify-end shrink-0">
                            <a href="{{ route('workouts.summary', $session->id) }}" class="flex-1 sm:flex-none text-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition-colors">
                                Kartu Ringkasan
                            </a>
                            <a href="{{ route('workouts.show', $session->id) }}" class="flex-1 sm:flex-none text-center px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition-colors">
                                Rincian Set
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $workouts->links() }}
        </div>
    @endif
</div>
@endsection
