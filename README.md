# TATA Apps - Aplikasi Cetak dan Desain Online

<div align="center">
  <img src="tataapps/assets/images/logo-icon.png" alt="TATA Logo" width="200"/>
  <br>
  <p><strong>Tempat Anda memesan jasa desain dan cetak dengan mudah, cepat, dan berkualitas</strong></p>
</div>

[![Flutter](https://img.shields.io/badge/Flutter-3.19.0-blue)](https://flutter.dev/)
[![Laravel](https://img.shields.io/badge/Laravel-10.0-red)](https://laravel.com/)
[![Firebase](https://img.shields.io/badge/Firebase-Latest-orange)](https://firebase.google.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

## 📑 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi dan Setup](#instalasi-dan-setup)
  - [Backend (Laravel)](#backend-laravel)
  - [Frontend Mobile (Flutter)](#frontend-mobile-flutter)
- [Petunjuk Penggunaan](#petunjuk-penggunaan)
- [Firebase Setup](#firebase-setup)
- [Troubleshooting](#troubleshooting)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## 🌟 Tentang Proyek

TATA Apps adalah platform cetak dan desain online yang menghubungkan pengguna dengan jasa desain profesional. Aplikasi ini terdiri dari backend Laravel untuk manajemen admin dan API, serta aplikasi mobile Flutter untuk pengguna akhir.

Aplikasi ini memungkinkan pengguna untuk memesan berbagai layanan desain seperti logo, banner, dan poster, melakukan pembayaran secara online, berkomunikasi dengan desainer melalui fitur chat, dan melacak status pesanan mereka.

## 🎯 Fitur Utama

- **Autentikasi Pengguna**: Login, register, lupa password, dan login dengan Google
- **Katalog Jasa Desain**: Pilihan berbagai jenis jasa desain dengan detail dan harga
- **Pemesanan**: Proses pemesanan dengan deskripsi kebutuhan dan referensi
- **Pembayaran Online**: Integrasi dengan berbagai metode pembayaran
- **Status Pesanan**: Tracking status pesanan secara real-time
- **Chat dengan Admin**: Komunikasi langsung dengan admin/desainer
- **Review dan Rating**: Pemberian rating dan ulasan setelah pesanan selesai
- **Notifikasi**: Notifikasi push untuk update pesanan dan chat
- **Panel Admin**: Manajemen pesanan, pengguna, dan pembayaran

## 💻 Teknologi yang Digunakan

### Backend

- [Laravel 10](https://laravel.com/)
- [MySQL](https://www.mysql.com/)
- [RESTful API](https://restfulapi.net/)
- [JWT Authentication](https://jwt.io/)

### Frontend Mobile

- [Flutter](https://flutter.dev/)
- [Dart](https://dart.dev/)
- [Provider](https://pub.dev/packages/provider) (State Management)
- [Dio](https://pub.dev/packages/dio) (HTTP Client)

### Cloud & Services

- [Firebase](https://firebase.google.com/)
  - Cloud Firestore (Chat)
  - Firebase Cloud Messaging (Notifikasi)
  - Firebase Authentication
- [Google Maps API](https://developers.google.com/maps)

## 📁 Struktur Proyek

Proyek ini terdiri dari dua komponen utama:

### 1. printing-commerce (Backend Laravel)

```
printing-commerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   ├── Models/
│   ├── Services/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
├── resources/
│   ├── views/
├── routes/
└── ...
```

### 2. tataapps (Frontend Flutter)

```
tataapps/
├── lib/
│   ├── BeforeLogin/          # Halaman sebelum login
│   ├── menu/                 # Halaman utama aplikasi
│   │   ├── JasaDesign/       # Halaman pemesanan jasa
│   │   ├── StatusPesanan/    # Tracking status pesanan
│   │   ├── ChatDetailScreen.dart
│   ├── models/               # Model data
│   ├── services/             # Service API dan Firebase
│   ├── sendApi/              # Komunikasi dengan backend
│   ├── helper/               # Helper functions
│   ├── src/                  # Shared components
│   ├── main.dart             # Entry point aplikasi
├── assets/                   # Asset gambar, font, dll
├── pubspec.yaml              # Dependency Flutter
└── ...
```

## 🚀 Instalasi dan Setup

### Backend Laravel

1. **Clone Repository:**

   ```bash
   git clone https://github.com/habstrakT808/TATA-apps.git
   cd TATA-apps/printing-commerce
   ```

2. **Install Dependencies:**

   ```bash
   composer install
   ```

3. **Setup Environment:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database:**

   - Edit file `.env` dengan konfigurasi database Anda:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tata_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi dan Seed Database:**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Jalankan Server:**
   ```bash
   php artisan serve
   ```
   Server akan berjalan di `http://localhost:8000`

### Frontend Mobile (Flutter)

1. **Masuk ke direktori aplikasi Flutter:**

   ```bash
   cd TATA-apps/tataapps
   ```

2. **Install Dependencies:**

   ```bash
   flutter pub get
   ```

3. **Konfigurasi Firebase:**

   - Buat proyek di [Firebase Console](https://console.firebase.google.com/)
   - Tambahkan aplikasi Android dan iOS
   - Download file konfigurasi (`google-services.json` dan `GoogleService-Info.plist`)
   - Tempatkan file-file tersebut di lokasi yang sesuai:
     - `android/app/google-services.json` (untuk Android)
     - `ios/Runner/GoogleService-Info.plist` (untuk iOS)

4. **Update Server URL:**

   - Edit file `lib/sendApi/Server.dart` dengan URL server Laravel Anda:

   ```dart
   static String baseUrl = 'http://192.168.1.x:8000'; // Ganti dengan IP atau domain server Anda
   ```

5. **Jalankan Aplikasi:**
   ```bash
   flutter run
   ```

## 📱 Petunjuk Penggunaan

### Admin Backend

1. Akses `http://[your-server-url]/login`
2. Login dengan kredensial admin:
   - Email: `admin@tata.com`
   - Password: `password`
3. Kelola pesanan, pengguna, dan konten dari dashboard admin

### Aplikasi Mobile

1. Buka aplikasi TATA Apps di perangkat mobile
2. Register akun baru atau login jika sudah memiliki akun
3. Browse katalog jasa desain yang tersedia
4. Pilih jasa yang diinginkan dan isi form pemesanan
5. Upload referensi desain jika diperlukan
6. Pilih metode pembayaran dan lakukan pembayaran
7. Pantau status pesanan melalui menu "Status Pesanan"
8. Komunikasikan kebutuhan desain dengan admin melalui fitur chat
9. Setelah pesanan selesai, berikan rating dan ulasan

## 🔥 Firebase Setup

### Konfigurasi Firebase Firestore (untuk fitur chat)

1. Buat proyek di [Firebase Console](https://console.firebase.google.com/)
2. Aktifkan Firebase Firestore
3. Setup aturan keamanan Firestore:

   ```
   rules_version = '2';
   service cloud.firestore {
     match /databases/{database}/documents {
       match /{document=**} {
         allow read, write: if true;  // Hanya untuk pengembangan
       }
     }
   }
   ```

   **CATATAN**: Untuk produksi, gunakan aturan keamanan yang lebih ketat. Lihat dokumentasi di `docs/flutter_chat_implementation.md`.

4. Aktifkan Firebase Authentication dengan metode Email/Password dan Google Sign-In
5. Aktifkan Firebase Cloud Messaging untuk notifikasi

## ⚠️ Troubleshooting

### Error Firebase Firestore: Permission Denied

Jika mendapat error "Missing or insufficient permissions":

1. Buka Firebase Console > Firestore > Rules
2. Update rules dengan mengizinkan akses selama pengembangan
3. Publish rules

### Error API: 401 Unauthorized

Periksa:

1. Token JWT yang digunakan valid
2. Header Authorization diset dengan benar
3. User memiliki izin yang cukup

### Error Flutter Build: Gradle Failure

1. Pastikan Android SDK dan build tools terinstal dengan benar
2. Update Gradle version di `android/gradle/wrapper/gradle-wrapper.properties`
3. Jalankan:
   ```bash
   flutter clean
   flutter pub get
   ```

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika Anda ingin berkontribusi:

1. Fork repositori
2. Buat branch fitur baru (`git checkout -b feature/amazing-feature`)
3. Commit perubahan Anda (`git commit -m 'Add some amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buka Pull Request

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">
  <p>© 2025 TATA Apps. All rights reserved.</p>
</div>
