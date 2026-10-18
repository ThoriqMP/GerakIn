@extends('layouts.app', ['title' => 'Jadwal & Template Rutin'])

@section('content')
<div class="space-y-6 pt-1 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-display font-black text-2xl text-slate-900">Jadwal Latihan (Routines)</h2>
            <p class="text-xs text-slate-600 mt-0.5">Template program latihan terstruktur untuk perencanaan.</p>
        </div>
        <a href="{{ route('routines.create') }}" class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-transform active:scale-95 flex items-center gap-1.5 self-start sm:self-auto">
            <span class="text-sm">+</span>
            <span>Buat Template</span>
        </a>
    </div>

    @if ($routines->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="font-bold text-slate-900 text-base">Belum Ada Template Jadwal</h3>
            <p class="text-xs text-slate-600 mt-1 mb-4">Buat template seperti Push Day, Pull Day, atau Leg Day untuk mempermudah pencatatan.</p>
            <a href="{{ route('routines.create') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-sm">
                + Buat Template Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($routines as $routine)
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: {{ $routine->color_tag ?? '#2563EB' }};"></span>
                                <span class="text-xs font-bold text-slate-600">{{ $routine->routineExercises->count() }} Gerakan</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('routines.edit', $routine->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('routines.destroy', $routine->id) }}" onsubmit="return confirm('Hapus template jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 text-xs" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="font-display font-black text-lg text-slate-900">{{ $routine->name }}</h3>
                        <p class="text-xs text-slate-600 mt-1 mb-4 line-clamp-2">{{ $routine->description ?? 'Rangkaian latihan terprogram untuk konsistensi gym.' }}</p>

                        <!-- Exercises chips -->
                        <div class="space-y-1.5 mb-5">
                            @foreach ($routine->routineExercises->take(4) as $re)
                                <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="font-medium text-slate-800 min-w-0 flex-1 truncate mr-2">{{ $re->exercise->name }}</span>
                                    <span class="text-slate-600 text-[11px] font-bold shrink-0">{{ $re->target_sets }}s × {{ $re->target_reps }}r</span>
                                </div>
                            @endforeach
                            @if ($routine->routineExercises->count() > 4)
                                <div class="text-[10px] text-slate-600 text-center">+ {{ $routine->routineExercises->count() - 4 }} gerakan lainnya</div>
                            @endif
                        </div>
                    </div>

                    <!-- Start workout from this routine -->
                    <form method="POST" action="{{ route('workouts.start') }}">
                        @csrf
                        <input type="hidden" name="routine_id" value="{{ $routine->id }}">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm active:scale-95 transition-all">
                            <span>Mulai Latihan Dari Template Ini</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
