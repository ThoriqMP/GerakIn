@extends('layouts.app', ['title' => 'Perpustakaan Gerakan'])

@section('content')
<div class="space-y-6 pt-1 pb-16" x-data="{ showCustomModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-display font-black text-2xl text-slate-900">Database Gerakan Olahraga</h2>
            <p class="text-xs text-slate-600 mt-0.5">Koleksi 100+ gerakan dengan panduan otot dan alat.</p>
        </div>
        <button type="button" @click="showCustomModal = true"
                class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-transform active:scale-95 flex items-center gap-1.5 self-start sm:self-auto">
            <span>+ Gerakan Kustom</span>
        </button>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
        <form method="GET" action="{{ route('exercises.index') }}" class="space-y-3">
            <!-- Search input -->
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama gerakan (misal: Squat, Deadlift, Bench Press)..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-600 text-xs focus:outline-none focus:border-blue-500">
                <svg class="w-4 h-4 text-slate-600 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Muscle Group Pills Filter -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-600 mb-1.5">Kelompok Otot:</div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($muscles as $m)
                        <button type="submit" name="muscle" value="{{ $m }}"
                                class="px-3 py-1 rounded-xl text-xs font-semibold transition-all {{ ($muscle ?? 'all') === $m ? 'bg-blue-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $m === 'all' ? 'Semua Otot' : ucfirst($m) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Equipment Filter -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-600 mb-1.5">Jenis Alat:</div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($equipments as $eq)
                        <button type="submit" name="equipment" value="{{ $eq }}"
                                class="px-3 py-1 rounded-xl text-xs font-semibold transition-all {{ ($equipment ?? 'all') === $eq ? 'bg-blue-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $eq === 'all' ? 'Semua Alat' : ucfirst($eq) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <!-- Exercises Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        @forelse ($exercises as $exercise)
            @php $pr = $exercise->getPersonalRecordForUser(Auth::id()); @endphp
            <a href="{{ route('exercises.show', $exercise->id) }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-blue-300 transition-colors flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $exercise->primary_muscle }}
                            </span>
                            @if ($exercise->user_id)
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                    Kustom
                                </span>
                            @endif
                        </div>
                        <span class="text-[11px] text-slate-600 capitalize">{{ $exercise->equipment }}</span>
                    </div>

                    <h3 class="font-display font-bold text-base text-slate-900 group-hover:text-blue-600 transition-colors">
                        {{ $exercise->name }}
                    </h3>

                    <p class="text-xs text-slate-600 mt-1 line-clamp-2">
                        {{ $exercise->instructions ?? 'Panduan gerakan latihan.' }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    @if ($pr)
                        <span class="font-bold text-amber-700 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            @if ($pr->weight_kg > 0)
                                <span>PR: {{ $pr->weight_kg }}kg × {{ $pr->reps }}</span>
                            @else
                                <span>PR: {{ $pr->reps }} Reps (Bodyweight)</span>
                            @endif
                        </span>
                    @else
                        <span class="text-slate-600 text-[11px]">Belum ada riwayat PR</span>
                    @endif
                    <span class="text-slate-600 group-hover:text-blue-600 font-semibold text-xs flex items-center gap-1">
                        <span>Detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
        @empty
            <div class="sm:col-span-2 bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Tidak Ditemukan Gerakan</h3>
                <p class="text-xs text-slate-600 mt-1">Coba sesuaikan kata kunci pencarian atau filter kelompok otot.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $exercises->links() }}
    </div>

    <!-- MODAL: CREATE CUSTOM EXERCISE -->
    <div x-show="showCustomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 max-w-md w-full shadow-xl relative"
             @click.away="showCustomModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="font-display font-black text-lg text-slate-900">Tambah Gerakan Kustom</h3>
                </div>
                <button type="button" @click="showCustomModal = false" class="text-slate-600 hover:text-slate-900 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('exercises.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Gerakan</label>
                    <input type="text" name="name" required placeholder="Misal: Landmine Press, Zercher Squat"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Otot Utama</label>
                        <select name="primary_muscle" required class="w-full py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="chest">Dada (Chest)</option>
                            <option value="back">Punggung (Back)</option>
                            <option value="legs">Kaki (Legs)</option>
                            <option value="shoulders">Bahu (Shoulders)</option>
                            <option value="arms">Lengan (Arms)</option>
                            <option value="core">Perut (Core)</option>
                            <option value="cardio">Kardio (Cardio)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Alat</label>
                        <select name="equipment" required class="w-full py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs">
                            <option value="barbell">Barbel</option>
                            <option value="dumbbell">Dumbbell</option>
                            <option value="machine">Mesin</option>
                            <option value="cable">Kabel</option>
                            <option value="bodyweight">Bodyweight</option>
                            <option value="cardio">Kardio</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="radio" name="category" value="strength" checked class="text-blue-600">
                            <span class="text-slate-800 font-medium">Beban (Strength)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="radio" name="category" value="cardio" class="text-blue-600">
                            <span class="text-slate-800 font-medium">Kardio</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petunjuk / Instruksi</label>
                    <textarea name="instructions" rows="2" placeholder="Cara eksekusi gerakan yang benar..."
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showCustomModal = false" class="w-1/3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                        Simpan Gerakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
