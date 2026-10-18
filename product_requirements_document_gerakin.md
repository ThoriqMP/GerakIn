# PRODUCT REQUIREMENTS DOCUMENT (PRD)
**Nama Produk:** GerakIn  
**Versi Dokumen:** 1.0  
**Platform:** Web & Progressive Web App (PWA)  
**Tech Stack Utama:** Laravel 11, Tailwind CSS, Alpine.js / Livewire, MySQL, Service Workers (PWA)

---

## 1. Ringkasan Eksekutif (Executive Summary)
### 1.1 Visi Produk
**GerakIn** adalah aplikasi pelacak aktivitas olahraga (*fitness & workout tracker*) berbasis *Progressive Web App* (PWA) yang dirancang untuk membantu pengguna mencatat, menganalisis, dan mempertahankan konsistensi olahraga mereka—baik latihan beban (*gym/strength training*) maupun kardio (*running/cycling*). Dengan pendekatan *mobile-first* dan kemampuan *offline-capable*, GerakIn memberikan pengalaman layaknya aplikasi *native* yang ringan, cepat, dan mudah diakses langsung dari *home screen* perangkat pengguna.

### 1.2 Identitas Visual & Branding
* **Nama Aplikasi:** GerakIn (Filosofi: Mengajak pengguna untuk mulai bergerak dan konsisten berolahraga).
* **Konsep Ikon/Logo:** Sepatu lari dinamis dengan efek kecepatan (*motion lines*) dan sambaran petir yang merepresentasikan energi, kecepatan, dan progres.
* **Palet Warna Utama (Tailwind Mapping):**
  * **Electric Blue (Primary):** `#0284C7` (`sky-600`) hingga `#1D4ED8` (`blue-700`) — melambangkan fokus, teknologi, dan stabilitas.
  * **Deep Navy (Background/Dark Mode):** `#0F172A` (`slate-900`) — memberikan kesan modern dan hemat baterai saat digunakan di dalam *gym*.
  * **Lightning Amber/Orange (Accent & CTA):** `#F59E0B` (`amber-500`) hingga `#EA580C` (`orange-600`) — melambangkan energi, pembakaran kalori, dan aksi utama.

---

## 2. Target Pengguna & User Persona
1. **The Gym Regular (Budi, 25 tahun):** Rutin angkat beban 4x seminggu. Membutuhkan aplikasi untuk mencatat beban (*kg*) dan *repetisi* dengan cepat di sela-sela waktu istirahat tanpa terganggu sinyal *basement gym* yang buruk.
2. **The Casual Runner & Habit Seeker (Siti, 29 tahun):** Ingin hidup lebih sehat dan menurunkan berat badan. Membutuhkan pengingat latihan, pencatatan durasi kardio, dan visualisasi *streak* mingguan agar tetap termotivasi.

---

## 3. Alur Pengguna (User Flow)

```
[Splash Screen PWA] 
       │
       ▼
[Belum Login?] ──(Ya)──► [Login / Register (Email atau Google OAuth)]
       │                                      │
    (Sudah)                          (User Baru Terdaftar)
       │                                      ▼
       │                      [3-Step Onboarding & Kustomisasi]
       │                      1. Data Fisik & Metrik (TB, BB, Usia)
       │                      2. Pilih Fitness Goal & Level
       │                      3. Preferensi Olahraga & Target Mingguan
       │                                      │
       ▼                                      ▼
[Dashboard Utama (Ringkasan, Streak, Muscle Heatmap, Quick Start)]
       │
       ├──► [Mulai Workout] ──► [Pilih Routine / Kosong] ──► [Input Set/Reps/Timer] ──► [Selesai & Summary Card]
       ├──► [Analitik & Progres] ──► [Grafik 1RM, Volume Latihan, Log Berat Badan]
       └──► [Profil & Pengaturan] ──► [Edit Kustomisasi, Tema, Export Data]
```

---

## 4. Spesifikasi Fitur Fungsional (Functional Requirements)

### 4.1 Modul Autentikasi & Kustomisasi Onboarding (Custom Auth & Profile Setup)
Alih-alih menggunakan halaman login bawaan yang kaku, modul ini dirancang khusus (*customized*) agar menyatu dengan *wizard* personalisasi pengguna.

* **FR-AUTH-01 (Custom Login & Register UI):**
  * Halaman autentikasi dengan desain *split-screen* (di desktop) atau *bottom-sheet card* (di mobile) menggunakan tema *Deep Navy* dan aksen *Amber*.
  * Mendukung pendaftaran menggunakan Email/Password, *Magic Link*, serta integrasi **Laravel Socialite** untuk *One-Tap Google Login*.
  * Validasi *real-time* (menggunakan Livewire) untuk ketersediaan *username* dan kekuatan *password*.
* **FR-AUTH-02 (Multi-Step Onboarding & Kustomisasi Awal):**
  Setelah mendaftar, pengguna wajib melewati 3 langkah cepat yang akan menentukan algoritma rekomendasi di *dashboard*:
  1. **Langkah 1: Metrik Tubuh Dasar:** Input jenis kelamin, usia, tinggi badan (`cm`), berat badan saat ini (`kg`), dan berat badan target.
  2. **Langkah 2: Tujuan Utama (*Fitness Goal*):** Pilihan kartu interaktif (*Lose Fat*, *Muscle Hypertrophy / Bulking*, *Endurance & Cardio*, atau *Stay Active*).
  3. **Langkah 3: Preferensi & Jadwal:** Memilih jenis olahraga favorit (Gym, Lari, Kalistenik, Sepeda), memilih hari latihan dalam seminggu (misal: Senin, Rabu, Jumat), serta memilih satuan metrik (`kg` vs `lbs`, `km` vs `miles`).
* **FR-AUTH-03 (Kustomisasi Tampilan & Pengaturan):**
  * Pengguna dapat memilih *Accent Badge* pada profil, mengatur durasi *default Rest Timer* (misal: 60 detik, 90 detik, 120 detik), dan memilih mode tampilan (*Dark Mode* sebagai *default* utama, atau *Light Mode*).

### 4.2 Modul Pencatatan Olahraga (Core Workout Logger)
* **FR-LOG-01 (Active Workout Session):**
  * Antarmuka pencatatan aktif yang tetap berjalan di bagian bawah layar (*minimizable bottom bar*).
  * Pencatatan *Strength Training*: Input nama gerakan, jumlah *Set*, *Weight* (Beban), *Reps* (Repetisi), dan indikator *RPE* (*Rate of Perceived Exertion* / tingkat kesulitan 1–10).
  * Pencatatan *Cardio*: Input durasi (menit/detik), jarak (`km`), dan estimasi kecepatan (*pace*).
* **FR-LOG-02 (Previous Set Reference & Smart Overload):**
  * Saat pengguna memilih gerakan (misal: *Bench Press*), sistem menampilkan angka beban dan repetisi dari sesi terakhir secara otomatis sebagai referensi (*ghost text* pada kolom input).
  * Memberikan *badge* notifikasi kecil jika pengguna berhasil memecahkan rekor angkatan terberat (*Personal Record / PR*).
* **FR-LOG-03 (Built-in Rest Timer):**
  * *Timer* hitung mundur otomatis menyala begitu pengguna mencentang (*check*) satu set selesai.
  * Dilengkapi tombol cepat `+15s`, `-15s`, dan `Skip`, serta memicu getaran (*Vibration API*) dan notifikasi suara saat waktu istirahat habis.

### 4.3 Modul Routine Builder & Exercise Library
* **FR-RTN-01 (Custom Routine Templates):**
  * Pengguna dapat membuat, menyimpan, dan mengelompokkan *template* latihan (contoh: *Push Day*, *Pull Day*, *Leg Day*, *Full Body*).
* **FR-RTN-02 (Database Gerakan & Custom Exercise):**
  * Tersedia *database* awal berisi 100+ gerakan olahraga lengkap dengan kategori otot (*Chest, Back, Legs, Shoulders, Arms, Core, Cardio*) dan jenis alat (*Barbell, Dumbbell, Machine, Bodyweight*).
  * Pengguna dapat menambahkan gerakan kustom (*Custom Exercise*) sendiri jika tidak ada di dalam daftar.

### 4.4 Modul Dashboard, Analitik & Gamifikasi
* **FR-DSH-01 (Interactive Muscle Heatmap):**
  * Visualisasi SVG anatomi tubuh manusia (tampak depan dan belakang) di *dashboard* yang berubah warna dari biru redup menjadi kuning/oranye menyala pada bagian otot yang sudah dilatih dalam 7 hari terakhir.
* **FR-DSH-02 (Streak & Gamification):**
  * Penghitung *Weekly Streak* (berapa minggu berturut-turut pengguna mencapai target hari latihan yang disetel saat *onboarding*).
  * Sistem *Badges* (contoh: *"First Sweat"*, *"10,000 Kg Club"*, *"Early Bird Workout"*).
* **FR-DSH-03 (Grafik Progres & Body Metrics):**
  * Grafik garis (*line chart*) untuk memantau tren berat badan, total volume angkatan mingguan (`Total Tonnage = Set × Reps × Beban`), dan estimasi *One Rep Max (1RM)*.
* **FR-DSH-04 (Shareable Workout Card):**
  * Setelah menyelesaikan sesi olahraga, aplikasi men-*generate* kartu ringkasan estetik berlogo **GerakIn** (menampilkan durasi, total beban yang diangkat, dan *PR* baru) yang siap diunduh atau dibagikan ke Instagram Story.

---

## 5. Arsitektur Teknis & Spesifikasi PWA (Laravel + Tailwind)

### 5.1 Tech Stack Detail
* **Backend Framework:** Laravel 11.x (PHP 8.3+)
* **Frontend & Interactivity:** Blade Templates + **Livewire 3** (untuk reaktivitas *single-page feel* tanpa kompleksitas *full SPA*) + **Alpine.js** (untuk *timer*, *offline state*, dan interaksi UI instan di sisi *client*).
* **Styling:** **Tailwind CSS v3.4+** (dengan plugin `@tailwindcss/forms` dan `@tailwindcss/typography`).
* **Database:** MySQL / PostgreSQL (Server) + **IndexedDB / LocalForage** (Browser Local Storage untuk mode *offline*).
* **Charts:** Chart.js atau ApexCharts yang diintegrasikan melalui komponen Alpine.js.

### 5.2 Spesifikasi Progressive Web App (PWA)
Agar **GerakIn** dapat di-install di Android, iOS, maupun Desktop dan berjalan mulus saat *offline* di area *gym*, berikut adalah spesifikasi wajib PWA:

1. **Web App Manifest (`public/manifest.json`):**
   ```json
   {
     "name": "GerakIn - Workout & Fitness Tracker",
     "short_name": "GerakIn",
     "start_url": "/dashboard?source=pwa",
     "display": "standalone",
     "background_color": "#0F172A",
     "theme_color": "#0284C7",
     "orientation": "portrait",
     "icons": [
       {
         "src": "/icons/icon-192x192.png",
         "sizes": "192x192",
         "type": "image/png",
         "purpose": "any maskable"
       },
       {
         "src": "/icons/icon-512x512.png",
         "sizes": "512x512",
         "type": "image/png",
         "purpose": "any maskable"
       }
     ]
   }
   ```
2. **Service Worker & Strategi Caching (`sw.js`):**
   * **Cache-First Strategy:** Untuk aset statis (CSS Tailwind hasil *build*, JS, Font, Ikon GerakIn, dan ilustrasi gerakan olahraga).
   * **Network-First with Offline Fallback:** Untuk halaman navigasi utama.
   * **Background Sync / Offline Workout Queue:** Saat pengguna mencatat *set* dan *repetisi* dalam kondisi tanpa sinyal, data disimpan sementara di `localStorage` / `IndexedDB` menggunakan Alpine.js. Begitu koneksi kembali terdeteksi (`window.addEventListener('online')`), aplikasi melakukan sinkronisasi *batch request* ke *endpoint* Laravel (`/api/workouts/sync`).
3. **Custom Install Prompt:**
   * Menampilkan *banner* khusus di dalam aplikasi ("Install GerakIn di HP kamu untuk akses *offline* lebih cepat!") yang menangkap *event* `beforeinstallprompt`.

---

## 6. Rancangan Skema Database (Database Schema)

Berikut adalah struktur tabel inti untuk migrasi Laravel:

| Nama Tabel | Kolom Utama | Keterangan Relasi / Fungsi |
| :--- | :--- | :--- |
| `users` | `id`, `name`, `username`, `email`, `password`, `google_id`, `avatar`, `onboarding_completed_at` | Tabel utama autentikasi pengguna. |
| `user_profiles` | `id`, `user_id`, `gender`, `birth_date`, `height_cm`, `current_weight_kg`, `target_weight_kg`, `fitness_goal`, `weekly_target_days`, `preferred_unit`, `default_rest_seconds`, `theme` | Menyimpan seluruh hasil kustomisasi *onboarding* dan preferensi pengguna. |
| `exercises` | `id`, `user_id` (*nullable*), `name`, `slug`, `primary_muscle`, `secondary_muscle`, `equipment`, `category` (*strength/cardio*), `instructions` | `user_id` bernilai `NULL` untuk gerakan bawaan sistem, dan berisi ID jika gerakan dibuat kustom oleh *user*. |
| `routines` | `id`, `user_id`, `name`, `description`, `color_tag`, `sort_order` | Menyimpan jadwal/template latihan (misal: *Push Day*). |
| `routine_exercises` | `id`, `routine_id`, `exercise_id`, `target_sets`, `target_reps`, `rest_seconds`, `sort_order` | Tabel *pivot* isi gerakan di dalam sebuah *routine*. |
| `workout_sessions` | `id`, `user_id`, `routine_id` (*nullable*), `title`, `started_at`, `ended_at`, `duration_seconds`, `total_volume_kg`, `notes`, `mood_rating` | Mencatat satu sesi olahraga yang telah dilakukan pengguna. |
| `workout_sets` | `id`, `workout_session_id`, `exercise_id`, `set_number`, `set_type` (*warmup/normal/drop/failure*), `weight_kg`, `reps`, `distance_km`, `duration_seconds`, `rpe`, `is_pr`, `is_completed` | Detail setiap *set* yang dilakukan di dalam sesi latihan. |
| `body_logs` | `id`, `user_id`, `logged_at`, `weight_kg`, `body_fat_percentage`, `photo_path` | Riwayat perubahan berat badan dan komposisi tubuh. |

---

## 7. Panduan Desain UI/UX (Tailwind CSS Design System)

Untuk menjaga konsistensi dengan logo **GerakIn** (biru elektrik dan petir kuning-oranye), konfigurasi `tailwind.config.js` diatur sebagai berikut:

```javascript
// tailwind.config.js
export default {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        gerakin: {
          navy: '#0B132B',      // Background utama (Dark Mode)
          card: '#1C2541',      // Background kartu / komponen
          blue: '#0077B6',      // Warna primer (Sepatu lari)
          cyan: '#00B4D8',      // Highlight sekunder (Motion lines)
          bolt: '#FFB703',      // Aksen kuning petir (Badge / Streak / PR)
          orange: '#FB8500',    // Tombol CTA utama (Mulai Latihan)
        }
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
        display: ['Outfit', 'sans-serif'], // Untuk angka timer, beban, dan judul
      }
    }
  }
}
```

### Komponen Kunci UI:
* **Bottom Navigation Bar:** Tetap berada di bagian bawah layar (`fixed bottom-0`) dengan 4 menu utama: *Beranda*, *Jadwal (Routines)*, tombol tengah menonjol **+ Mulai Gerak** (berwarna `gerakin-orange` dengan ikon petir), *Statistik*, dan *Profil*.
* **Input Angka Ramah Jempol (*Thumb-Friendly Inputs*):** Di dalam *gym*, pengguna sering berkeringat. Kolom input beban (`kg`) dan `reps` dibuat berukuran besar dengan tombol stepper `+` dan `-` serta *checkbox* penyelesaian set yang memiliki efek animasi *pulse* saat ditekan.

---

## 8. Roadmap Pengembangan (Development Phases)

### Fase 1: Foundation & Custom Auth (Minggu 1–2)
* Setup proyek Laravel 11, Tailwind CSS, dan Livewire 3.
* Implementasi halaman Login, Register, Google OAuth, dan **3-Step Onboarding Customization**.
* Konfigurasi dasar PWA (`manifest.json`, pembuatan *icon sizes* dari logo utama, dan registrasi *Service Worker*).

### Fase 2: Core Workout & Routine Engine (Minggu 3–5)
* Seeding *database* 100+ gerakan olahraga dasar.
* Pembuatan modul *Routine Builder* (CRUD jadwal latihan).
* Pembuatan layar *Active Workout Logger* lengkap dengan *Rest Timer* (Alpine.js) dan kalkulasi *Personal Record (PR)* otomatis.

### Fase 3: Dashboard, Analytics & Offline Sync (Minggu 6–7)
* Integrasi komponen SVG *Muscle Heatmap* dan grafik progres menggunakan Chart.js.
* Implementasi *Offline Queue* menggunakan `IndexedDB` agar pencatatan tetap aman saat sinyal terputus.
* Fitur pencatatan berat badan (*Body Metrics*) dan sistem *Weekly Streak*.

### Fase 4: Polishing, Shareable Card & Testing (Minggu 8)
* Pembuatan generator gambar ringkasan latihan (*HTML5 Canvas* / `html-to-image`) untuk dibagikan ke media sosial.
* Audit performa *Lighthouse PWA* (memastikan skor PWA, Performance, dan Accessibility > 90).
* *User Acceptance Testing (UAT)* dan *deployment* ke server produksi (dengan wajib SSL/HTTPS agar fitur PWA dan *Service Worker* aktif).