# Panduan Setup Production Server Lokal

## 1. Install FFmpeg (Wajib untuk Video)
FFmpeg diperlukan agar sistem bisa memproses video yang diupload (resize, convert ke format TV-friendly).

### Langkah-langkah:
1. **Download FFmpeg**:
   - Buka: [https://www.gyan.dev/ffmpeg/builds/ffmpeg-release-essentials.zip](https://www.gyan.dev/ffmpeg/builds/ffmpeg-release-essentials.zip)
   - Download file ZIP tersebut.

2. **Extract & Simpan**:
   - Extract isi ZIP.
   - Rename folder hasil extract menjadi `ffmpeg`.
   - Pindahkan folder `ffmpeg` ke `C:\laragon\bin\` (sehingga ada `C:\laragon\bin\ffmpeg\bin\ffmpeg.exe`).
   - *Catatan: Anda bisa taruh di mana saja, tapi folder Laragon tempat yang rapi.*

3. **Setting Environment Variable (Agar bisa dipanggil command line)**:
   - Tekan tombol **Start**, ketik "env", pilih **"Edit the system environment variables"**.
   - Klik tombol **"Environment Variables..."** di kanan bawah.
   - Di bagian bawah (**System variables**), cari variable bernama `Path`, klik **Edit**.
   - Klik **New**, lalu paste lokasi folder bin ffmpeg, misal:
     `C:\laragon\bin\ffmpeg\bin`
   - Klik OK di semua window.

4. **Verifikasi**:
   - Tutup semua terminal/cmd yang terbuka.
   - Buka terminal baru (atau PowerShell di Laragon).
   - Ketik: `ffmpeg -version`
   - Jika muncul tulisan versi, berarti BERHASIL! ✅

---

## 2. Konfigurasi Jaringan (Agar bisa diakses TV)

Agar Smart TV bisa membuka aplikasi, Anda tidak bisa menggunakan `localhost`. Anda harus menggunakan IP Address komputer server.

### Langkah-langkah:
1. **Cek IP Address Server**:
   - Buka terminal, ketik: `ipconfig`
   - Cari **IPv4 Address** (biasanya `192.168.1.x` atau `192.168.0.x`).
   - Contoh: `192.168.1.50`

2. **Update Konfigurasi Aplikasi (.env)**:
   - Buka file `.env` di folder project.
   - Ubah `APP_URL` menjadi IP dan Port tersebut.
   - Contoh:
     ```ini
     APP_URL=http://192.168.1.50:8000
     ```
     *(Ganti 192.168.1.50 dengan IP komputer Anda)*

3. **Restart Aplikasi**:
   - Di terminal Laravel, jalankan:
     ```bash
     php artisan config:clear
     php artisan queue:restart
     ```
   - Regenerate playlist agar URL di dalamnya terupdate:
     ```bash
     php artisan tinker --execute="App\Jobs\GeneratePlaylistJob::dispatchSync();"
     ```

### 3. Akses dari TV
- Pastikan TV dan Komputer terhubung di WiFi/LAN yang sama.
- Buka browser di TV.
- Ketik alamat: `http://192.168.1.50:8000/signage` (sesuai IP tadi).
- Klik layar untuk memulai (agar audio aktif).
