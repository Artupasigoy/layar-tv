# Arsitektur & Stabilitas Sistem Signage

Dokumen ini menjelaskan mengapa sistem ini aman dan optimal untuk penggunaan 24/7 di Smart TV (bahkan model low-end).

## 1. Beban CPU Sangat Rendah (Zero Polling)
- **Cara Lama**: Browser terus-menerus bertanya ke server "Ada update ga?" tiap 5 detik. Ini membuat CPU TV panas dan network sibuk.
- **Cara Baru**: Browser hanya mendownload `playlist.json` **sekali** saat mulai. Browser tidak melakukan request ke server lagi sampai jadwal reload (misal: 6 jam lagi).
- **Efek**: CPU TV idle 99% waktu, hanya bekerja saat ganti slide.

## 2. Optimasi Media di Server
- **Masalah Umum**: User upload foto 4K (10MB) atau video 60fps yang berat. TV bisa crash saat mencoba merender.
- **Solusi Kita**: Semua media **diproses ulang** oleh server (FFmpeg/GD) sebelum masuk playlist.
  - Video dikonversi ke **1080p, 30fps, H.264** (Format paling standar/ringan).
  - Gambar di-resize pas ke 1920x1080.
- **Efek**: TV tidak perlu kerja keras men-downscale gambar/video. Playback mulus.

## 3. Anti-Memory Leak (Auto Reload)
- **Fakta**: Semua browser (Chrome/Tizen/WebOS), jika tidak dimatikan berhari-hari, akan memakan RAM perlahan (memory leak) hingga crash/blank.
- **Fitur Pengaman**: Sistem ini punya fitur **`auto_reload_interval`**.
  - Default: Setiap 6 jam, browser akan refresh sebentar (kedip hitam 1 detik).
  - Ini "membersihkan" RAM TV agar kembali segar.
  - Menjamin alat tidak perlu direstart manual oleh manusia.

## 4. Vanilla Javascript (Tanpa Framework Berat)
- Player di TV (`signage.blade.php`) dibuat murni menggunakan Javascript dasar.
- Tidak ada *React*, *Vue*, atau *Livewire* yang berjalan di sisi TV.
- Ukuran script sangat kecil (< 50kb), loading instant.

## Kesimpulan
Sistem ini sudah memenuhi **Best Practice** untuk *Unattended Digital Signage*.
- **Hemat Bandwidth**: Hanya download saat update.
- **Hemat Listrik/Panas**: CPU kerja minimal.
- **Tahan Lama**: Fitur auto-reload mencegah crash jangka panjang.
