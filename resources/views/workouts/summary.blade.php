@extends('layouts.app', ['title' => 'Ringkasan Latihan'])

@section('content')
<div class="space-y-6 pt-1 pb-16" x-data="{
    downloading: false,
    copied: false,

    async downloadCard() {
        this.downloading = true;
        try {
            const node = document.getElementById('shareableCard');
            const dataUrl = await window.htmlToImage.toPng(node, { quality: 0.95 });
            const link = document.createElement('a');
            link.download = 'GerakIn_{{ Str::slug($session->title) }}_{{ date('Ymd') }}.png';
            link.href = dataUrl;
            link.click();
        } catch (error) {
            console.error('Error generating image:', error);
            alert('Gagal membuat gambar kartu. Silakan ambil screenshot layar.');
        } finally {
            this.downloading = false;
        }
    },

    async shareCard() {
        if (navigator.share) {
            try {
                const node = document.getElementById('shareableCard');
                const blob = await window.htmlToImage.toBlob(node);
                const file = new File([blob], 'gerakin_workout.png', { type: 'image/png' });
                await navigator.share({
                    title: 'Latihan di GerakIn',
                    text: 'Baru saja menyelesaikan {{ $session->title }} dengan total beban {{ number_format($session->total_volume_kg) }} kg di GerakIn!',
                    files: [file]
                });
            } catch (err) {
                console.log('Share canceled or not supported with files:', err);
            }
        } else {
            this.downloadCard();
        }
    }
}">

    <div class="text-center max-w-lg mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold mb-2">
            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Sesi Selesai & Tersimpan</span>
        </div>
        <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900">Sesi Latihan Tersimpan</h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">
            Kartu ringkasan latihan minimalis siap diunduh atau dibagikan.
        </p>
    </div>

    <!-- CLEAN MINIMALIST SHAREABLE WORKOUT CARD -->
    <div class="flex justify-center">
        <div id="shareableCard" class="w-full max-w-sm rounded-[32px] p-5 sm:p-6 bg-white border border-slate-200 shadow-xl relative overflow-hidden text-slate-900">
            <!-- Subtle Header Accent -->
            <div class="relative z-10 flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <img src="/icons/icon-72x72.png" alt="GerakIn" class="w-10 h-10 rounded-xl shadow-sm border border-slate-200">
                    <div>
                        <div class="font-display font-black text-base text-slate-900 leading-tight">
                            <span>Gerak</span><span class="text-blue-600">In</span>
                        </div>
                        <div class="text-[10px] text-slate-600">Workout Tracker</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-xs text-slate-800">{{ $session->user->name }}</div>
                    <div class="text-[10px] text-slate-600">{{ $session->started_at->isoFormat('D MMM Y') }}</div>
                </div>
            </div>

            <!-- Card Session Title -->
            <div class="relative z-10 my-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Sesi Latihan</span>
                <h3 class="font-display font-black text-xl text-slate-900 leading-snug mt-0.5">
                    {{ $session->title }}
                </h3>
            </div>

            <!-- Big Stat Highlights -->
            <div class="relative z-10 grid grid-cols-2 gap-3 my-4">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="text-[10px] uppercase font-bold text-slate-600">Total Volume</div>
                    <div class="mt-1">
                        <span class="font-display font-black text-2xl text-slate-900">{{ number_format($session->total_volume_kg) }}</span>
                        <span class="text-xs font-semibold text-blue-600">kg</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="text-[10px] uppercase font-bold text-slate-600">Durasi</div>
                    <div class="mt-1">
                        <span class="font-display font-black text-2xl text-slate-900">{{ round($session->duration_seconds / 60) }}</span>
                        <span class="text-xs font-semibold text-slate-600">Menit</span>
                    </div>
                </div>
            </div>

            <!-- PR Highlight (if any) -->
            @if ($prSets->isNotEmpty())
                <div class="relative z-10 p-3 rounded-2xl bg-amber-50 border border-amber-200 my-3">
                    <div class="flex items-center gap-1.5 text-amber-800 text-xs font-bold uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Rekor Pribadi (PR) Baru!</span>
                    </div>
                    <div class="mt-1 space-y-1">
                        @foreach ($prSets as $prSet)
                            <div class="text-xs text-slate-800 font-bold flex items-center justify-between">
                                <span>{{ $prSet->exercise->name }}</span>
                                @if ($prSet->weight_kg > 0)
                                    <span class="text-amber-700">{{ $prSet->weight_kg }} kg × {{ $prSet->reps }}</span>
                                @else
                                    <span class="text-amber-700">{{ $prSet->reps }} Reps (Bodyweight)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Exercises List Summary -->
            <div class="relative z-10 my-4 space-y-2">
                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-600">Rincian Gerakan:</div>
                <div class="space-y-1.5 max-h-48 overflow-hidden text-xs">
                    @foreach ($session->sets->groupBy('exercise_id') as $exId => $sets)
                        @php $firstEx = $sets->first()->exercise; @endphp
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-medium text-slate-800 min-w-0 flex-1 truncate mr-2">{{ $firstEx->name }}</span>
                            <span class="text-blue-600 text-[11px] font-bold shrink-0">{{ $sets->where('is_completed', true)->count() }} Sets</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Muscles Trained Tags -->
            @if (!empty($musclesTrained))
                <div class="relative z-10 pt-3 border-t border-slate-100 flex flex-wrap gap-1.5">
                    @foreach ($musclesTrained as $muscle)
                        <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $muscle }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Bottom Branding Watermark -->
            <div class="relative z-10 mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-600 font-medium">
                <span>#GerakInTracker</span>
                <span>gerakin.app</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="max-w-sm mx-auto flex flex-col gap-2.5">
        <button type="button" @click="downloadCard()" :disabled="downloading"
                class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span x-text="downloading ? 'Sedang Memproses...' : 'Unduh Gambar Kartu (PNG)'"></span>
        </button>

        <button type="button" @click="shareCard()"
                class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm border border-slate-200 shadow-sm flex items-center justify-center gap-2 transition-colors">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            <span>Bagikan ke Story / Teman</span>
        </button>

        <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 rounded-2xl text-slate-600 hover:text-slate-800 font-semibold text-xs text-center transition-colors">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
