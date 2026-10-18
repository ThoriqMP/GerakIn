@props(['muscleHeat' => [], 'muscleSetsCount' => [], 'gender' => 'male'])

@php
    $getMuscleClass = function($muscle) use ($muscleHeat) {
        $level = $muscleHeat[$muscle] ?? 'inactive';
        return match($level) {
            'high' => 'muscle-heat-high',
            'medium' => 'muscle-heat-medium',
            'low' => 'muscle-heat-low',
            default => 'muscle-heat-inactive',
        };
    };

    $getSets = function($muscle) use ($muscleSetsCount) {
        return $muscleSetsCount[$muscle] ?? 0;
    };
@endphp

<div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm" x-data="{
    gender: '{{ $gender === 'female' ? 'female' : 'male' }}',
    activeTab: 'front',
    selectedMuscle: null,
    muscleData: {
        chest: { name: 'Dada (Pectoralis Major)', sets: {{ $getSets('chest') }}, desc: 'Melatih dada atas, tengah, dan bawah' },
        back: { name: 'Punggung (Lats & Traps)', sets: {{ $getSets('back') }}, desc: 'Melatih sayap lats, trapezius, & rhomboids' },
        shoulders: { name: 'Bahu (Deltoids)', sets: {{ $getSets('shoulders') }}, desc: 'Melatih deltoid anterior, lateral, & posterior' },
        arms: { name: 'Lengan (Biceps, Triceps & Forearms)', sets: {{ $getSets('arms') }}, desc: 'Melatih bisep, trisep, & lengan bawah' },
        legs: { name: 'Kaki (Quadriceps, Glutes, Hamstrings & Calves)', sets: {{ $getSets('legs') }}, desc: 'Melatih paha depan, bokong, paha belakang, & betis' },
        core: { name: 'Perut (Abdominals & Obliques)', sets: {{ $getSets('core') }}, desc: 'Melatih rectus abdominis & obliques samping' }
    }
}">
    <!-- Header with Gender Mode & Front/Back switchers -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h3 class="font-display font-bold text-base sm:text-lg text-slate-900">Peta Stimulasi Otot (Heatmap Anatomi)</h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Visualisasi anatomi otot tubuh yang dilatih 7 hari terakhir</p>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <!-- Gender Mode Toggle (Pria / Wanita) -->
            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button type="button" @click="gender = 'male'"
                        :class="gender === 'male' ? 'bg-white text-blue-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Pria</span>
                </button>
                <button type="button" @click="gender = 'female'"
                        :class="gender === 'female' ? 'bg-white text-blue-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Wanita</span>
                </button>
            </div>

            <!-- View Toggle (Tampak Depan / Tampak Belakang) -->
            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button type="button" @click="activeTab = 'front'"
                        :class="activeTab === 'front' ? 'bg-white text-blue-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1 rounded-lg text-xs font-semibold transition-all">
                    Tampak Depan
                </button>
                <button type="button" @click="activeTab = 'back'"
                        :class="activeTab === 'back' ? 'bg-white text-blue-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1 rounded-lg text-xs font-semibold transition-all">
                    Tampak Belakang
                </button>
            </div>
        </div>
    </div>

    <!-- Realistic Anatomical Human Body Heatmap Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
        <!-- SVG Interactive Container -->
        <div class="md:col-span-7 flex justify-center py-4 relative bg-slate-50/80 rounded-2xl border border-slate-100 min-h-[420px] overflow-hidden">

            <!-- ================= 1. MALE FRONT VIEW (REFERENSI GAMBAR KIRI) ================= -->
            <svg x-show="gender === 'male' && activeTab === 'front'" class="w-72 h-[410px] select-none transition-all duration-300" viewBox="0 0 280 430" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- BASE SOLID SILHOUETTE (ELIMINATES ALL VOIDS / GAPS) -->
                <path d="M 140 16 C 126 16 122 26 122 38 C 122 52 128 60 134 64 C 130 68 126 74 122 78 C 104 80 84 84 68 90 C 62 96 60 110 64 124 C 68 136 70 142 66 160 C 62 176 56 186 50 206 C 44 224 40 240 38 248 C 34 254 36 266 42 276 C 46 282 50 280 54 272 C 58 262 60 252 62 244 C 68 226 72 204 78 178 C 84 158 88 144 94 130 C 96 142 96 162 98 180 C 100 198 102 212 104 222 C 96 236 88 260 88 286 C 88 304 94 316 98 326 C 96 338 92 356 94 372 C 96 384 100 392 104 394 C 100 400 96 410 94 418 C 98 420 118 420 124 418 C 124 410 122 400 120 394 C 124 386 126 368 126 352 C 126 338 124 328 124 326 C 128 316 134 300 136 284 C 138 268 138 252 140 236 C 142 252 142 268 144 284 C 146 300 152 316 156 326 C 156 328 154 338 154 352 C 154 368 156 386 160 394 C 158 400 156 410 156 418 C 162 420 182 420 186 418 C 184 410 180 400 176 394 C 180 392 184 384 186 372 C 188 356 184 338 182 326 C 186 316 192 304 192 286 C 192 260 184 236 176 222 C 178 212 180 198 182 180 C 184 162 184 142 186 130 C 192 144 196 158 202 178 C 208 204 212 226 218 244 C 220 252 222 262 226 272 C 230 280 234 282 238 276 C 244 266 246 254 242 248 C 240 240 236 224 230 206 C 224 186 218 176 214 160 C 210 142 212 136 216 124 C 220 110 218 96 212 90 C 196 84 176 80 158 78 C 154 74 150 68 146 64 C 152 60 158 52 158 38 C 158 26 154 16 140 16 Z" fill="#E2E8F0" />

                <!-- Head & Jaw Contour -->
                <path d="M 140 18 C 126 18 122 26 122 38 C 122 52 128 60 134 64 L 140 66 L 146 64 C 152 60 158 52 158 38 C 158 26 154 18 140 18 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                
                <!-- Neck & Trapezius Top Collar -->
                <path d="M 134 64 L 126 78 L 138 78 L 140 66 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 146 64 L 140 66 L 142 78 L 154 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 126 78 L 122 78 L 108 84 L 126 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 154 78 L 158 78 L 172 84 L 154 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- SHOULDERS (Deltoids - Anterior & Lateral) -->
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 108 82 C 92 84 76 88 68 94 C 60 102 60 118 64 126 C 68 136 76 138 80 130 C 86 118 94 104 108 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 172 82 C 188 84 204 88 212 94 C 220 102 220 118 216 126 C 212 136 204 138 200 130 C 194 118 186 104 172 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />

                <!-- CHEST (Pectoralis Major Left & Right) -->
                <path @mouseenter="selectedMuscle = 'chest'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'chest'"
                      d="M 138 80 L 108 82 C 98 98 92 112 94 124 C 98 134 114 134 138 128 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('chest') }}" />
                <path @mouseenter="selectedMuscle = 'chest'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'chest'"
                      d="M 142 80 L 172 82 C 182 98 188 112 186 124 C 182 134 166 134 142 128 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('chest') }}" />

                <!-- ARMS (Biceps, Brachialis & Forearms) -->
                <!-- Left Biceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 80 130 C 74 138 68 148 66 160 C 64 172 70 178 78 178 C 84 168 88 152 94 130 C 88 132 84 132 80 130 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Brachialis / Outer Arm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 64 126 C 60 136 60 150 64 164 L 66 160 C 68 148 74 138 80 130 L 64 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 64 164 C 58 178 52 192 48 210 C 44 224 40 238 38 248 L 54 246 C 58 232 66 206 74 186 L 78 178 C 72 178 68 174 64 164 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Hand -->
                <path d="M 38 248 C 34 254 36 266 42 276 C 46 282 50 280 54 272 C 58 262 56 254 54 246 L 38 248 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- Right Biceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 200 130 C 206 138 212 148 214 160 C 216 172 210 178 202 178 C 196 168 192 152 186 130 C 192 132 196 132 200 130 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Brachialis / Outer Arm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 216 126 C 220 136 220 150 216 164 L 214 160 C 212 148 206 138 200 130 L 216 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 216 164 C 222 178 228 192 232 210 C 236 224 240 238 242 248 L 226 246 C 222 232 214 206 206 186 L 202 178 C 208 178 212 174 216 164 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Hand -->
                <path d="M 242 248 C 246 254 244 266 238 276 C 234 282 230 280 226 272 C 222 262 224 254 226 246 L 242 248 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- CORE (Rectus Abdominis 6-Pack, Obliques & Serratus) -->
                <g @mouseenter="selectedMuscle = 'core'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'core'">
                    <!-- Upper Abs Pair -->
                    <path d="M 116 130 H 138 V 146 H 116 C 114 140 114 136 116 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 130 H 164 C 166 136 166 140 164 146 H 142 V 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Middle Abs Pair -->
                    <path d="M 116 148 H 138 V 166 H 116 C 114 160 114 154 116 148 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 148 H 164 C 166 154 166 160 164 166 H 142 V 148 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Lower Abs Pair -->
                    <path d="M 118 168 H 138 V 188 H 120 C 118 180 118 174 118 168 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 168 H 162 C 162 174 162 180 160 188 H 142 V 168 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Pyramidalis / Pelvic V-Taper -->
                    <path d="M 120 190 H 138 L 138 226 C 132 216 124 204 120 190 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 190 H 160 C 156 204 148 216 142 226 L 142 190 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    
                    <!-- Left External Obliques & Serratus -->
                    <path d="M 94 130 C 102 132 110 130 114 130 C 112 148 114 166 116 188 C 116 202 118 214 120 224 C 112 220 106 210 104 196 C 100 178 98 160 94 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Left Rib Serratus Slips -->
                    <path d="M 94 130 C 90 144 92 160 96 172 L 98 168 C 96 156 96 142 98 132 L 94 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />

                    <!-- Right External Obliques & Serratus -->
                    <path d="M 186 130 C 178 132 170 130 166 130 C 168 148 166 166 164 188 C 164 202 162 214 160 224 C 168 220 174 210 176 196 C 180 178 182 160 186 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Right Rib Serratus Slips -->
                    <path d="M 186 130 C 190 144 188 160 184 172 L 182 168 C 184 156 184 142 182 132 L 186 130 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                </g>

                <!-- LEGS (Quadriceps, Vastus Lateralis, Rectus Femoris, Vastus Medialis Teardrop, Calves) -->
                <g @mouseenter="selectedMuscle = 'legs'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'legs'">
                    <!-- Left Vastus Lateralis (Outer Quad) -->
                    <path d="M 104 224 C 94 238 88 262 88 286 C 88 304 94 316 98 326 L 104 326 C 102 312 104 286 108 264 C 110 248 114 234 116 226 L 104 224 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Rectus Femoris (Center Quad) -->
                    <path d="M 116 226 C 114 234 110 248 108 264 C 104 286 102 312 104 326 L 118 326 C 120 312 120 286 122 264 C 124 248 126 234 126 226 L 116 226 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Vastus Medialis Teardrop -->
                    <path d="M 126 264 C 126 278 128 296 132 312 C 130 322 124 326 118 326 C 120 312 120 286 122 264 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Adductors (Inner Thigh Cleft) -->
                    <path d="M 138 234 C 132 248 128 266 126 278 L 126 264 C 126 250 128 238 132 230 L 138 234 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Vastus Lateralis (Outer Quad) -->
                    <path d="M 176 224 C 186 238 192 262 192 286 C 192 304 186 316 182 326 L 176 326 C 178 312 176 286 172 264 C 170 248 166 234 164 226 L 176 224 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Rectus Femoris (Center Quad) -->
                    <path d="M 164 226 C 166 234 170 248 172 264 C 176 286 178 312 176 326 L 162 326 C 160 312 160 286 158 264 C 156 248 154 234 154 226 L 164 226 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Vastus Medialis Teardrop -->
                    <path d="M 154 264 C 154 278 152 296 148 312 C 150 322 156 326 162 326 C 160 312 160 286 158 264 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Adductors (Inner Thigh Cleft) -->
                    <path d="M 142 234 C 148 248 152 266 154 278 L 154 264 C 154 250 152 238 148 230 L 142 234 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Knees / Patella -->
                    <path d="M 104 326 C 104 332 110 336 114 336 C 118 336 124 332 124 326 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 156 326 C 156 332 162 336 166 336 C 170 336 176 332 176 326 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Left Calves (Tibialis & Gastrocnemius) -->
                    <path d="M 104 336 C 96 348 92 364 94 376 C 96 386 100 392 104 394 L 110 394 C 106 382 106 366 108 348 L 114 336 L 104 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 114 336 L 108 348 C 106 366 106 382 110 394 L 116 394 C 118 382 120 366 122 348 L 124 336 L 114 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 124 336 L 122 348 C 120 366 118 382 116 394 L 120 394 C 124 386 126 368 126 352 C 126 342 125 338 124 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Calves (Tibialis & Gastrocnemius) -->
                    <path d="M 176 336 C 184 348 188 364 186 376 C 184 386 180 392 176 394 L 170 394 C 174 382 174 366 172 348 L 166 336 L 176 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 166 336 L 172 348 C 174 366 174 382 170 394 L 164 394 C 162 382 160 366 158 348 L 156 336 L 166 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 156 336 L 158 348 C 160 366 162 382 164 394 L 160 394 C 156 386 154 368 154 352 C 154 342 155 338 156 336 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Feet -->
                    <path d="M 104 394 L 94 418 C 98 420 118 420 124 418 L 120 394 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 176 394 L 186 418 C 182 420 162 420 156 418 L 160 394 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                </g>
            </svg>
    
            <!-- ================= 2. MALE BACK VIEW (REFERENSI GAMBAR KIRI) ================= -->
            <svg x-show="gender === 'male' && activeTab === 'back'" class="w-72 h-[410px] select-none transition-all duration-300" viewBox="0 0 280 430" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <!-- BASE SOLID SILHOUETTE (ELIMINATES ALL VOIDS / GAPS) -->
                <path d="M 140 16 C 126 16 122 26 122 38 C 122 52 128 60 134 64 C 130 68 126 74 122 78 C 104 80 84 84 68 90 C 62 96 60 110 64 124 C 68 136 70 142 66 160 C 62 176 56 186 50 206 C 44 224 40 240 38 248 C 34 254 36 266 42 276 C 46 282 50 280 54 272 C 58 262 60 252 62 244 C 68 226 72 204 78 178 C 84 158 88 144 94 130 C 96 142 96 162 98 180 C 100 198 102 212 104 222 C 96 236 88 260 88 286 C 88 304 94 316 98 326 C 96 338 92 356 94 372 C 96 384 100 392 104 394 C 100 400 96 410 94 418 C 98 420 118 420 124 418 C 124 410 122 400 120 394 C 124 386 126 368 126 352 C 126 338 124 328 124 326 C 128 316 134 300 136 284 C 138 268 138 252 140 236 C 142 252 142 268 144 284 C 146 300 152 316 156 326 C 156 328 154 338 154 352 C 154 368 156 386 160 394 C 158 400 156 410 156 418 C 162 420 182 420 186 418 C 184 410 180 400 176 394 C 180 392 184 384 186 372 C 188 356 184 338 182 326 C 186 316 192 304 192 286 C 192 260 184 236 176 222 C 178 212 180 198 182 180 C 184 162 184 142 186 130 C 192 144 196 158 202 178 C 208 204 212 226 218 244 C 220 252 222 262 226 272 C 230 280 234 282 238 276 C 244 266 246 254 242 248 C 240 240 236 224 230 206 C 224 186 218 176 214 160 C 210 142 212 136 216 124 C 220 110 218 96 212 90 C 196 84 176 80 158 78 C 154 74 150 68 146 64 C 152 60 158 52 158 38 C 158 26 154 16 140 16 Z" fill="#E2E8F0" />

                <!-- Head & Occipital -->
                <path d="M 140 18 C 126 18 122 26 122 38 C 122 52 128 60 134 64 L 140 66 L 146 64 C 152 60 158 52 158 38 C 158 26 154 18 140 18 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- BACK (Trapezius Kite Diamond, Lats, Infraspinatus, Erector Spinae) -->
                <!-- Left Trapezius (Kite Upper/Mid) -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 138 66 L 134 64 L 122 78 L 108 82 C 114 96 124 118 138 162 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <!-- Right Trapezius (Kite Upper/Mid) -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 142 66 L 146 64 L 158 78 L 172 82 C 166 96 156 118 142 162 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- SHOULDERS (Posterior Deltoids) -->
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 108 82 L 68 90 C 60 98 60 114 64 126 C 68 134 76 136 80 128 C 88 118 96 104 108 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 172 82 L 212 90 C 220 98 220 114 216 126 C 212 134 204 136 200 128 C 192 118 184 104 172 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />

                <!-- BACK: Infraspinatus & Teres Major (Scapular Muscles) -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 108 82 C 96 104 88 118 80 128 C 88 136 94 140 100 144 C 112 138 124 126 130 112 L 108 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 172 82 C 184 104 192 118 200 128 C 192 136 186 140 180 144 C 168 138 156 126 150 112 L 172 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- BACK: Latissimus Dorsi (Lats V-Taper Wings) -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 100 144 C 94 140 88 136 80 128 C 76 136 78 150 82 168 C 88 184 94 200 98 210 C 110 212 122 208 138 196 L 138 162 C 124 154 112 148 100 144 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 180 144 C 186 140 192 136 200 128 C 204 136 202 150 198 168 C 192 184 186 200 182 210 C 170 212 158 208 142 196 L 142 162 C 156 154 168 148 180 144 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- BACK: Lower Back / Erector Spinae Columns -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 126 196 H 138 V 222 H 126 C 124 212 124 204 126 196 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 142 196 H 154 C 156 204 156 212 154 222 H 142 V 196 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- ARMS (Triceps & Forearms Posterior) -->
                <!-- Left Triceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 64 126 C 60 136 60 152 64 164 L 66 174 L 78 174 C 76 160 76 142 80 128 L 64 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Posterior Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 64 164 C 58 178 52 192 48 210 C 44 224 40 238 38 248 L 54 246 C 58 232 66 206 74 186 L 66 174 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Hand Posterior -->
                <path d="M 38 248 C 34 254 36 266 42 276 C 46 282 50 280 54 272 C 58 262 56 254 54 246 L 38 248 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- Right Triceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 216 126 C 220 136 220 152 216 164 L 214 174 L 202 174 C 204 160 204 142 200 128 L 216 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Posterior Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 216 164 C 222 178 228 192 232 210 C 236 224 240 238 242 248 L 226 246 C 222 232 214 206 206 186 L 214 174 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Hand Posterior -->
                <path d="M 242 248 C 246 254 244 266 238 276 C 234 282 230 280 226 272 C 222 262 224 254 226 246 L 242 248 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- LEGS (Glutes, Hamstrings & Calves Posterior) -->
                <g @mouseenter="selectedMuscle = 'legs'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'legs'">
                    <!-- Left Gluteus Maximus Cheek -->
                    <path d="M 104 222 C 94 232 88 248 90 262 C 92 274 104 278 118 278 C 128 278 136 272 138 266 L 138 222 L 126 222 C 114 222 108 222 104 222 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Gluteus Maximus Cheek -->
                    <path d="M 176 222 C 186 232 192 248 190 262 C 188 274 176 278 162 278 C 152 278 144 272 142 266 L 142 222 L 154 222 C 166 222 172 222 176 222 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Left Hamstrings (Biceps Femoris & Semitendinosus) -->
                    <path d="M 90 262 C 88 276 90 298 94 316 L 108 316 C 104 298 104 284 104 278 L 90 262 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 104 278 C 104 284 104 298 108 316 L 122 316 C 126 298 130 284 136 270 L 118 278 L 104 278 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Inner Thigh Medial -->
                    <path d="M 136 270 C 132 282 128 298 126 316 L 122 316 C 126 298 130 284 136 270 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Hamstrings (Biceps Femoris & Semitendinosus) -->
                    <path d="M 190 262 C 192 276 190 298 186 316 L 172 316 C 176 298 176 284 176 278 L 190 262 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 176 278 C 176 284 176 298 172 316 L 158 316 C 154 298 150 284 144 270 L 162 278 L 176 278 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Inner Thigh Medial -->
                    <path d="M 144 270 C 148 282 152 298 154 316 L 158 316 C 154 298 150 284 144 270 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Popliteal Fossa (Back of Knees) -->
                    <path d="M 94 316 H 126 V 326 H 94 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 154 316 H 186 V 326 H 154 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Left Calves (Posterior Gastrocnemius Dual Bellies & Achilles Tendon) -->
                    <path d="M 94 326 C 90 340 90 358 94 372 C 98 382 104 386 108 388 L 110 356 C 104 346 100 336 94 326 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 126 326 C 128 340 128 358 124 372 C 120 382 116 386 112 388 L 110 356 C 116 346 120 336 126 326 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Achilles Tendon -->
                    <path d="M 108 388 H 112 L 114 418 H 104 L 108 388 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Right Calves (Posterior Gastrocnemius Dual Bellies & Achilles Tendon) -->
                    <path d="M 186 326 C 190 340 190 358 186 372 C 182 382 176 386 172 388 L 170 356 C 176 346 180 336 186 326 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 154 326 C 152 340 152 358 156 372 C 160 382 164 386 168 388 L 170 356 C 164 346 160 336 154 326 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Achilles Tendon -->
                    <path d="M 168 388 H 172 L 176 418 H 166 L 168 388 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Feet Heels -->
                    <path d="M 94 418 H 124 V 420 H 94 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 156 418 H 186 V 420 H 156 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                </g>
            </svg>
    
            <!-- ================= 3. FEMALE FRONT VIEW (REFERENSI GAMBAR KANAN) ================= -->
            <svg x-show="gender === 'female' && activeTab === 'front'" class="w-72 h-[410px] select-none transition-all duration-300" viewBox="0 0 280 430" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <!-- BASE SOLID SILHOUETTE (FEMALE ATHLETIC SILHOUETTE, ZERO VOIDS) -->
                <path d="M 140 18 C 128 18 124 28 124 38 C 124 50 128 58 134 62 C 132 66 130 72 128 78 C 114 80 96 84 80 90 C 74 96 72 108 76 122 C 78 134 78 142 74 158 C 70 174 66 186 60 204 C 54 222 50 238 48 246 C 44 252 46 264 52 274 C 56 280 60 278 64 270 C 66 260 68 250 70 242 C 76 224 80 202 86 178 C 92 158 94 144 98 132 C 100 144 100 160 102 176 C 104 192 106 206 108 216 C 98 230 92 254 92 280 C 92 300 98 314 102 324 C 100 336 96 354 98 370 C 100 382 104 390 108 392 C 104 398 100 408 98 416 C 102 418 118 418 124 416 C 124 408 122 398 120 392 C 124 384 126 368 126 352 C 126 338 124 326 124 324 C 128 314 134 298 136 282 C 138 266 138 250 140 236 C 142 250 142 266 144 282 C 146 298 152 314 156 324 C 156 326 154 338 154 352 C 154 368 156 384 160 392 C 158 398 156 408 156 416 C 162 418 178 418 182 416 C 180 408 176 398 172 392 C 176 390 180 382 182 370 C 184 354 180 336 178 324 C 182 314 188 300 188 280 C 188 254 182 230 172 216 C 174 206 176 192 178 176 C 180 160 180 144 182 132 C 186 144 188 158 194 178 C 200 202 204 224 210 242 C 212 250 214 260 216 270 C 220 278 224 280 228 274 C 234 264 236 252 232 246 C 230 238 226 222 220 204 C 214 186 210 174 206 158 C 202 142 202 134 204 122 C 208 108 206 96 200 90 C 184 84 166 80 152 78 C 150 72 148 66 146 62 C 152 58 156 50 156 38 C 156 28 152 18 140 18 Z" fill="#E2E8F0" />

                <!-- Head & Jaw -->
                <path d="M 140 18 C 128 18 124 28 124 38 C 124 50 128 58 134 62 L 140 64 L 146 62 C 152 58 156 50 156 38 C 156 28 152 18 140 18 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- Neck & Clavicle Collar -->
                <path d="M 134 62 L 128 78 L 138 78 L 140 64 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 146 62 L 140 64 L 142 78 L 152 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 128 78 L 118 82 L 128 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                <path d="M 152 78 L 162 82 L 152 78 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- SHOULDERS (Feminine Deltoids) -->
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 118 82 C 104 84 90 88 82 94 C 74 102 74 116 78 124 C 82 132 88 134 92 126 C 98 116 106 102 118 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 162 82 C 176 84 190 88 198 94 C 206 102 206 116 202 124 C 198 132 192 134 188 126 C 182 116 174 102 162 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />

                <!-- CHEST (Bust & Pectoralis Major Feminine Contour) -->
                <path @mouseenter="selectedMuscle = 'chest'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'chest'"
                      d="M 138 80 L 118 82 C 108 96 102 110 102 124 C 104 136 116 138 138 132 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('chest') }}" />
                <path @mouseenter="selectedMuscle = 'chest'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'chest'"
                      d="M 142 80 L 162 82 C 172 96 178 110 178 124 C 176 136 164 138 142 132 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('chest') }}" />

                <!-- ARMS (Biceps & Forearms Feminine) -->
                <!-- Left Biceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 92 126 C 86 134 80 144 76 156 C 74 168 80 174 86 174 C 92 164 94 148 98 132 L 92 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 76 156 C 70 172 64 188 60 206 C 56 220 52 236 48 246 L 64 242 C 68 228 74 204 82 182 L 86 174 C 80 174 78 168 76 156 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Hand -->
                <path d="M 48 246 C 44 252 46 264 52 274 C 56 280 60 278 64 270 C 66 260 64 250 64 242 L 48 246 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- Right Biceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 188 126 C 194 134 200 144 204 156 C 206 168 200 174 194 174 C 188 164 186 148 182 132 L 188 126 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 204 156 C 210 172 216 188 220 206 C 224 220 228 236 232 246 L 216 242 C 212 228 206 204 198 182 L 194 174 C 200 174 202 168 204 156 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Hand -->
                <path d="M 232 246 C 236 252 234 264 228 274 C 224 280 220 278 216 270 C 214 260 216 250 216 242 L 232 246 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- CORE (Toned Feminine Abdomen & Slender Waist) -->
                <g @mouseenter="selectedMuscle = 'core'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'core'">
                    <!-- Upper Abdomen -->
                    <path d="M 120 134 H 138 V 162 H 120 C 118 152 118 142 120 134 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 134 H 160 C 162 142 162 152 160 162 H 142 V 134 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Lower Abdomen & Pelvic Taper -->
                    <path d="M 120 164 H 138 L 138 226 C 130 216 122 200 120 164 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <path d="M 142 164 H 160 C 158 200 150 216 142 226 L 142 164 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />

                    <!-- Left Hourglass Waist & Obliques -->
                    <path d="M 102 132 C 108 134 116 134 120 134 C 118 152 118 174 120 212 C 114 216 108 214 108 216 C 104 200 102 182 102 174 C 100 156 100 142 102 132 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                    <!-- Right Hourglass Waist & Obliques -->
                    <path d="M 178 132 C 172 134 164 134 160 134 C 162 152 162 174 160 212 C 166 216 172 214 172 216 C 176 200 178 182 178 174 C 180 156 180 142 178 132 Z" class="muscle-anatomy-path {{ $getMuscleClass('core') }}" />
                </g>

                <!-- LEGS (Shapely Quads, Knees, Calves Feminine) -->
                <g @mouseenter="selectedMuscle = 'legs'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'legs'">
                    <!-- Left Outer Thigh (Vastus Lateralis Curving to Pelvic Flare) -->
                    <path d="M 108 216 C 98 230 92 254 92 280 C 92 300 98 314 102 324 L 110 324 C 108 310 108 284 112 262 C 114 246 118 230 120 220 L 108 216 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Central Thigh (Rectus Femoris) -->
                    <path d="M 120 220 C 118 230 114 246 112 262 C 108 284 108 310 110 324 L 122 324 C 124 310 124 284 126 262 C 126 246 128 230 130 224 L 120 220 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Medial Thigh & Adductors -->
                    <path d="M 138 234 C 132 248 128 266 126 278 L 126 262 C 126 248 128 238 132 230 L 138 234 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 126 262 C 126 278 128 296 132 312 C 130 322 126 324 122 324 C 124 310 124 284 126 262 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Outer Thigh (Vastus Lateralis Curving to Pelvic Flare) -->
                    <path d="M 172 216 C 182 230 188 254 188 280 C 188 300 182 314 178 324 L 170 324 C 172 310 172 284 168 262 C 166 246 162 230 160 220 L 172 216 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Central Thigh (Rectus Femoris) -->
                    <path d="M 160 220 C 162 230 166 246 168 262 C 172 284 172 310 170 324 L 158 324 C 156 310 156 284 154 262 C 154 246 152 230 150 224 L 160 220 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Medial Thigh & Adductors -->
                    <path d="M 142 234 C 148 248 152 266 154 278 L 154 262 C 154 248 152 238 148 230 L 142 234 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 154 262 C 154 278 152 296 148 312 C 150 322 154 324 158 324 C 156 310 156 284 154 262 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Knees / Patella -->
                    <path d="M 106 324 C 106 330 112 334 116 334 C 120 334 124 330 124 324 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 156 324 C 156 330 160 334 164 334 C 168 334 174 330 174 324 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Left Calves (Tibialis & Gastrocnemius Feminine) -->
                    <path d="M 106 334 C 98 346 96 364 98 374 C 100 384 104 390 108 392 L 114 392 C 110 380 110 364 112 346 L 116 334 L 106 334 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 116 334 L 112 346 C 110 364 110 380 114 392 L 120 392 C 124 384 126 368 126 352 C 126 342 125 336 124 334 L 116 334 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Calves (Tibialis & Gastrocnemius Feminine) -->
                    <path d="M 174 334 C 182 346 184 364 182 374 C 180 384 176 390 172 392 L 166 392 C 170 380 170 364 168 346 L 164 334 L 174 334 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 164 334 L 168 346 C 170 364 170 380 166 392 L 160 392 C 156 384 154 368 154 352 C 154 342 155 336 156 334 L 164 334 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Feet -->
                    <path d="M 108 392 L 98 416 C 102 418 118 418 124 416 L 120 392 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 172 392 L 182 416 C 178 418 162 418 156 416 L 160 392 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                </g>
            </svg>
    
            <!-- ================= 4. FEMALE BACK VIEW (REFERENSI GAMBAR KANAN) ================= -->
            <svg x-show="gender === 'female' && activeTab === 'back'" class="w-72 h-[410px] select-none transition-all duration-300" viewBox="0 0 280 430" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <!-- BASE SOLID SILHOUETTE (FEMALE POSTERIOR, ZERO VOIDS) -->
                <path d="M 140 18 C 128 18 124 28 124 38 C 124 50 128 58 134 62 C 132 66 130 72 128 78 C 114 80 96 84 80 90 C 74 96 72 108 76 122 C 78 134 78 142 74 158 C 70 174 66 186 60 204 C 54 222 50 238 48 246 C 44 252 46 264 52 274 C 56 280 60 278 64 270 C 66 260 68 250 70 242 C 76 224 80 202 86 178 C 92 158 94 144 98 132 C 100 144 100 160 102 176 C 104 192 106 206 108 216 C 98 230 92 254 92 280 C 92 300 98 314 102 324 C 100 336 96 354 98 370 C 100 382 104 390 108 392 C 104 398 100 408 98 416 C 102 418 118 418 124 416 C 124 408 122 398 120 392 C 124 384 126 368 126 352 C 126 338 124 326 124 324 C 128 314 134 298 136 282 C 138 266 138 250 140 236 C 142 250 142 266 144 282 C 146 298 152 314 156 324 C 156 326 154 338 154 352 C 154 368 156 384 160 392 C 158 398 156 408 156 416 C 162 418 178 418 182 416 C 180 408 176 398 172 392 C 176 390 180 382 182 370 C 184 354 180 336 178 324 C 182 314 188 300 188 280 C 188 254 182 230 172 216 C 174 206 176 192 178 176 C 180 160 180 144 182 132 C 186 144 188 158 194 178 C 200 202 204 224 210 242 C 212 250 214 260 216 270 C 220 278 224 280 228 274 C 234 264 236 252 232 246 C 230 238 226 222 220 204 C 214 186 210 174 206 158 C 202 142 202 134 204 122 C 208 108 206 96 200 90 C 184 84 166 80 152 78 C 150 72 148 66 146 62 C 152 58 156 50 156 38 C 156 28 152 18 140 18 Z" fill="#E2E8F0" />

                <!-- Head & Occipital -->
                <path d="M 140 18 C 128 18 124 28 124 38 C 124 50 128 58 134 62 L 140 64 L 146 62 C 152 58 156 50 156 38 C 156 28 152 18 140 18 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- BACK (Trapezius Diamond, Lats, Scapula Feminine) -->
                <!-- Left Trapezius -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 138 64 L 134 62 L 128 78 L 118 82 C 122 96 128 118 138 156 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <!-- Right Trapezius -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 142 64 L 146 62 L 152 78 L 162 82 C 158 96 152 118 142 156 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- SHOULDERS (Posterior Deltoids Feminine) -->
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 118 82 L 82 92 C 74 100 74 114 78 124 C 82 132 88 134 92 126 C 98 116 106 102 118 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />
                <path @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'shoulders'"
                      d="M 162 82 L 198 92 C 206 100 206 114 202 124 C 198 132 192 134 188 126 C 182 116 174 102 162 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('shoulders') }}" />

                <!-- BACK: Scapular Muscles -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 118 82 C 106 102 98 116 92 126 C 98 132 102 136 108 140 C 118 134 126 124 130 112 L 118 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 162 82 C 174 102 182 116 188 126 C 182 132 178 136 172 140 C 162 134 154 124 150 112 L 162 82 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- BACK: Latissimus Dorsi Feminine Taper -->
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 108 140 C 102 136 98 132 92 126 C 88 134 90 148 94 164 C 98 178 102 192 106 204 C 114 206 124 202 138 190 L 138 156 C 126 148 116 144 108 140 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />
                <path @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'back'"
                      d="M 172 140 C 178 136 182 132 188 126 C 192 134 190 148 186 164 C 182 178 178 192 174 204 C 166 206 156 202 142 190 L 142 156 C 154 148 164 144 172 140 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('back') }}" />

                <!-- ARMS (Posterior Triceps & Forearms Feminine) -->
                <!-- Left Triceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 78 124 C 74 134 74 148 78 160 L 80 170 L 90 170 C 88 156 88 140 92 126 L 78 124 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 78 160 C 72 174 66 188 62 206 C 58 220 54 236 48 246 L 64 242 C 68 228 74 204 82 182 L 80 170 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Left Hand -->
                <path d="M 48 246 C 44 252 46 264 52 274 C 56 280 60 278 64 270 C 66 260 64 250 64 242 L 48 246 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- Right Triceps -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 202 124 C 206 134 206 148 202 160 L 200 170 L 190 170 C 192 156 192 140 188 126 L 202 124 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Forearm -->
                <path @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'arms'"
                      d="M 202 160 C 208 174 214 188 218 206 C 222 220 226 236 232 246 L 216 242 C 212 228 206 204 198 182 L 200 170 Z"
                      class="muscle-anatomy-path {{ $getMuscleClass('arms') }}" />
                <!-- Right Hand -->
                <path d="M 232 246 C 236 252 234 264 228 274 C 224 280 220 278 216 270 C 214 260 216 250 216 242 L 232 246 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                <!-- LEGS (Full Rounded Glutes, Hamstrings & Calves Posterior Feminine) -->
                <g @mouseenter="selectedMuscle = 'legs'" @mouseleave="selectedMuscle = null" @click="selectedMuscle = 'legs'">
                    <!-- Left Full Rounded Gluteus Cheek -->
                    <path d="M 106 216 C 96 226 90 242 92 258 C 94 272 106 278 120 278 C 128 278 136 272 138 266 L 138 216 L 126 216 C 116 216 110 216 106 216 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Full Rounded Gluteus Cheek -->
                    <path d="M 174 216 C 184 226 190 242 188 258 C 186 272 174 278 160 278 C 152 278 144 272 142 266 L 142 216 L 154 216 C 164 216 170 216 174 216 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Left Hamstrings Posterior -->
                    <path d="M 92 258 C 90 274 92 296 96 314 L 110 314 C 106 296 106 282 106 278 L 92 258 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 106 278 C 106 282 106 296 110 314 L 122 314 C 126 296 130 282 136 270 L 120 278 L 106 278 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Medial Hamstrings -->
                    <path d="M 136 270 C 132 282 128 296 126 314 L 122 314 C 126 296 130 282 136 270 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Right Hamstrings Posterior -->
                    <path d="M 188 258 C 190 274 188 296 184 314 L 170 314 C 174 296 174 282 174 278 L 188 258 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 174 278 C 174 282 174 296 170 314 L 158 314 C 154 296 150 282 144 270 L 160 278 L 174 278 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Medial Hamstrings -->
                    <path d="M 144 270 C 148 282 152 296 154 314 L 158 314 C 154 296 150 282 144 270 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />

                    <!-- Popliteal Fossa back of knees -->
                    <path d="M 96 314 H 126 V 324 H 96 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 154 314 H 184 V 324 H 154 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Left Calves (Posterior Gastrocnemius Dual Bellies Feminine) -->
                    <path d="M 96 324 C 92 338 92 356 96 370 C 100 380 106 384 110 386 L 112 354 C 106 344 102 334 96 324 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 126 324 C 128 338 128 356 124 370 C 120 380 116 384 112 386 L 112 354 C 118 344 122 334 126 324 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Left Achilles Tendon -->
                    <path d="M 110 386 H 114 L 116 416 H 106 L 110 386 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Right Calves (Posterior Gastrocnemius Dual Bellies Feminine) -->
                    <path d="M 184 324 C 188 338 188 356 184 370 C 180 380 174 384 170 386 L 168 354 C 174 344 178 334 184 324 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <path d="M 154 324 C 152 338 152 356 156 370 C 160 380 164 384 168 386 L 168 354 C 162 344 158 334 154 324 Z"
                          class="muscle-anatomy-path {{ $getMuscleClass('legs') }}" />
                    <!-- Right Achilles Tendon -->
                    <path d="M 166 386 H 170 L 174 416 H 164 L 166 386 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />

                    <!-- Feet Heels -->
                    <path d="M 98 416 H 124 V 418 H 98 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                    <path d="M 156 416 H 182 V 418 H 156 Z" fill="#E2E8F0" stroke="#FFFFFF" stroke-width="1.2" />
                </g>
            </svg>
            </div>

        <!-- Sidebar Anatomy Details & Intensity Legends -->
        <div class="md:col-span-5 space-y-4">
            <!-- Dynamic Info Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs transition-all">
                <template x-if="selectedMuscle">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] uppercase tracking-wider font-bold text-blue-700">Otot Terpilih</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-blue-600 shadow-xs" x-text="muscleData[selectedMuscle].sets + ' Set Minggu Ini'"></span>
                        </div>
                        <h4 class="font-display font-bold text-base text-slate-900 leading-snug" x-text="muscleData[selectedMuscle].name"></h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed" x-text="muscleData[selectedMuscle].desc"></p>
                    </div>
                </template>
                <template x-if="!selectedMuscle">
                    <div>
                        <div class="text-xs text-slate-600 leading-relaxed flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Arahkan kursor atau sentuh bagian otot pada ilustrasi tubuh untuk melihat jumlah set latihan dan intensitasnya.</span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Quick Muscle Breakdown List -->
            <div class="space-y-1.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Status Latihan 7 Hari:</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div @mouseenter="selectedMuscle = 'chest'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Dada (Chest)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('chest') }}s</span>
                    </div>
                    <div @mouseenter="selectedMuscle = 'back'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Punggung (Back)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('back') }}s</span>
                    </div>
                    <div @mouseenter="selectedMuscle = 'shoulders'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Bahu (Delts)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('shoulders') }}s</span>
                    </div>
                    <div @mouseenter="selectedMuscle = 'arms'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Lengan (Arms)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('arms') }}s</span>
                    </div>
                    <div @mouseenter="selectedMuscle = 'legs'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Kaki (Legs)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('legs') }}s</span>
                    </div>
                    <div @mouseenter="selectedMuscle = 'core'" @mouseleave="selectedMuscle = null" class="p-2 rounded-xl bg-white border border-slate-200 flex items-center justify-between hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="font-medium text-slate-700">Perut (Core)</span>
                        <span class="font-bold text-blue-600">{{ $getSets('core') }}s</span>
                    </div>
                </div>
            </div>

            <!-- Heat Intensity Legend -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Tingkat Intensitas Stimulasi:</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="w-3.5 h-3.5 rounded-md bg-slate-200 border border-white"></span>
                        <span class="text-slate-600 text-[11px]">Istirahat (0 Set)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="w-3.5 h-3.5 rounded-md bg-blue-300 border border-white"></span>
                        <span class="text-slate-700 text-[11px]">Ringan (1-3 Set)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="w-3.5 h-3.5 rounded-md bg-blue-500 border border-white"></span>
                        <span class="text-slate-700 text-[11px]">Sedang (4-8 Set)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="w-3.5 h-3.5 rounded-md bg-blue-700 border border-white"></span>
                        <span class="text-slate-700 text-[11px]">Optimal (&gt;8 Set)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
