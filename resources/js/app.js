import './bootstrap';
import { Chart, registerables } from 'chart.js';
import confetti from 'canvas-confetti';
import * as htmlToImage from 'html-to-image';
import Alpine from 'alpinejs';

Chart.register(...registerables);
window.Chart = Chart;
window.confetti = confetti;
window.htmlToImage = htmlToImage;

// Sound synthesizer using Web Audio API for timer finish chime
window.playTimerChime = function () {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        osc.type = 'sine';
        // Elegant ascending two-tone beep (600Hz -> 900Hz)
        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
        osc.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.15); // A5

        gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);

        osc.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        osc.start();
        osc.stop(audioCtx.currentTime + 0.6);
    } catch (e) {
        console.warn('AudioContext not allowed without user interaction:', e);
    }

    // Trigger phone vibration if supported
    if ('vibrate' in navigator) {
        try {
            navigator.vibrate([200, 100, 300]);
        } catch (e) {
            // ignore
        }
    }
};

// PWA Install prompt handling
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    window.dispatchEvent(new CustomEvent('pwa-installable'));
});

window.triggerPwaInstall = async () => {
    if (!deferredPrompt) {
        alert('Untuk menginstal GerakIn: Di Safari iOS tekan tombol Bagikan > Tambah ke Layar Utama. Di Chrome Android tekan menu titik tiga > Pasang aplikasi.');
        return;
    }
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === 'accepted') {
        deferredPrompt = null;
    }
};

// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((reg) => {
            console.log('GerakIn ServiceWorker registered:', reg.scope);
        }).catch((err) => {
            console.warn('GerakIn ServiceWorker registration failed:', err);
        });
    });
}

// Offline Workout Queue & Synchronization
const OFFLINE_QUEUE_KEY = 'gerakin_offline_queue';

window.getOfflineQueue = function() {
    try {
        return JSON.parse(localStorage.getItem(OFFLINE_QUEUE_KEY) || '[]');
    } catch (e) {
        return [];
    }
};

window.queueOfflineWorkout = function(workoutData) {
    const queue = window.getOfflineQueue();
    queue.push(workoutData);
    localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(queue));
};

window.syncOfflineWorkouts = async function() {
    const queue = window.getOfflineQueue();
    if (!queue || queue.length === 0) return;

    if (!navigator.onLine) {
        console.log('Still offline, sync postponed.');
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('/api/workouts/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ workouts: queue })
        });

        if (res.ok) {
            localStorage.removeItem(OFFLINE_QUEUE_KEY);
            console.log('All offline workouts synced successfully!');
            window.dispatchEvent(new CustomEvent('offline-sync-success'));
        }
    } catch (err) {
        console.warn('Sync attempt failed:', err);
    }
};

window.addEventListener('online', () => {
    window.syncOfflineWorkouts();
});

// Setup Alpine
window.Alpine = Alpine;
if (!window.AlpineStarted) {
    Alpine.start();
    window.AlpineStarted = true;
}
