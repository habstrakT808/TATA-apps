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
- [Persyaratan Sistem](#persyaratan-sistem)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi dan Setup](#instalasi-dan-setup)
  - [Backend (Laravel)](#backend-laravel)
  - [Frontend Mobile (Flutter)](#frontend-mobile-flutter)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
  - [Menjalankan Backend](#menjalankan-backend)
  - [Menjalankan Frontend](#menjalankan-frontend)
  - [Build untuk Produksi](#build-untuk-produksi)
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

## 💻 Persyaratan Sistem

### Untuk Pengembangan

- **Backend**:

  - PHP 8.1 atau lebih tinggi
  - Composer
  - MySQL 5.7 atau lebih tinggi
  - Web server (Apache/Nginx)
  - Laravel 10.x

- **Frontend**:
  - Flutter SDK 3.19.0 atau lebih tinggi
  - Dart 3.5.0 atau lebih tinggi
  - Android Studio / VS Code
  - Android SDK (untuk pengembangan Android)
  - Xcode (untuk pengembangan iOS, hanya di macOS)
  - Git

### Untuk Deployment

- **Backend**:

  - Server dengan PHP 8.1+
  - MySQL Database
  - Web server (Apache/Nginx)
  - SSL Certificate (disarankan)

- **Frontend**:
  - Google Play Developer Account (untuk Android)
  - Apple Developer Account (untuk iOS)
  - Firebase Project

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

### Persiapan Awal

1. **Instal Software yang Diperlukan**:

   - [Git](https://git-scm.com/downloads)
   - [Composer](https://getcomposer.org/download/)
   - [PHP 8.1+](https://www.php.net/downloads)
   - [MySQL](https://dev.mysql.com/downloads/mysql/)
   - [Flutter SDK](https://flutter.dev/docs/get-started/install)
   - [Android Studio](https://developer.android.com/studio) atau [VS Code](https://code.visualstudio.com/)

2. **Siapkan Environment Flutter**:
   ```bash
   flutter doctor
   ```
   Pastikan semua persyaratan terpenuhi sebelum melanjutkan.

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

   - Buat database MySQL baru untuk aplikasi
   - Edit file `.env` dengan konfigurasi database Anda:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tata_app
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Migrasi dan Seed Database:**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Setup Storage Link:**

   ```bash
   php artisan storage:link
   ```

7. **Konfigurasi CORS (untuk API):**

   Edit file `config/cors.php` untuk mengizinkan akses dari aplikasi Flutter:

   ```php
   'allowed_origins' => ['*'], // Untuk pengembangan, ganti dengan domain spesifik untuk produksi
   ```

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
   - Tambahkan aplikasi Android dan iOS:

     **Untuk Android:**

     - Gunakan package name: `com.example.tata` (sesuaikan dengan yang ada di `android/app/build.gradle`)
     - Download file `google-services.json`
     - Tempatkan di `android/app/google-services.json`

     **Untuk iOS:**

     - Gunakan bundle ID: `com.example.tata` (sesuaikan dengan yang ada di `ios/Runner.xcodeproj/project.pbxproj`)
     - Download file `GoogleService-Info.plist`
     - Tempatkan di `ios/Runner/GoogleService-Info.plist`

4. **Update Server URL:**

   - Edit file `lib/sendApi/Server.dart` dengan URL server Laravel Anda:

   ```dart
   static String baseUrl = 'http://192.168.1.x:8000'; // Ganti dengan IP atau domain server Anda
   ```

5. **Konfigurasi Icon Aplikasi:**

   Icon aplikasi sudah dikonfigurasi di `flutter_launcher_icons.yaml`. Untuk mengupdate icon, jalankan:

   ```bash
   flutter pub run flutter_launcher_icons
   ```

## 🚀 Menjalankan Aplikasi

### Menjalankan Backend

1. **Start MySQL Server**:

   Pastikan MySQL server Anda berjalan.

2. **Jalankan Laravel Server:**

   ```bash
   cd TATA-apps/printing-commerce
   php artisan serve --host=0.0.0.0
   ```

   Server akan berjalan di `http://localhost:8000` dan dapat diakses dari perangkat lain di jaringan yang sama.

3. **Cek API Endpoint:**

   Akses `http://localhost:8000/api/check` untuk memastikan API berjalan dengan baik.

### Menjalankan Frontend

1. **Periksa Perangkat Tersedia:**

   ```bash
   cd TATA-apps/tataapps
   flutter devices
   ```

2. **Jalankan di Emulator/Simulator:**

   ```bash
   flutter run
   ```

   Atau pilih perangkat spesifik:

   ```bash
   flutter run -d <device_id>
   ```

3. **Hot Reload:**

   Saat aplikasi berjalan, tekan `r` di terminal untuk hot reload atau `R` untuk hot restart.

### Build untuk Produksi

#### Android

1. **Buat Keystore (jika belum ada):**

   ```bash
   keytool -genkey -v -keystore tata.keystore -alias tata -keyalg RSA -keysize 2048 -validity 10000
   ```

2. **Konfigurasi Keystore:**

   Buat file `android/key.properties` dengan isi:

   ```
   storePassword=<password>
   keyPassword=<password>
   keyAlias=tata
   storeFile=<path_to_keystore>/tata.keystore
   ```

3. **Build APK:**

   ```bash
   flutter build apk --release
   ```

   File APK akan tersedia di `build/app/outputs/flutter-apk/app-release.apk`

4. **Build App Bundle:**

   ```bash
   flutter build appbundle --release
   ```

   File AAB akan tersedia di `build/app/outputs/bundle/release/app-release.aab`

#### iOS (hanya di macOS)

1. **Setup Signing:**

   Buka `ios/Runner.xcworkspace` di Xcode dan konfigurasi signing.

2. **Build IPA:**

   ```bash
   flutter build ios --release
   ```

3. **Archive di Xcode:**

   Buka Xcode, pilih `Product > Archive` dan ikuti langkah-langkah untuk distribusi.

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

   **CATATAN**: Untuk produksi, gunakan aturan keamanan yang lebih ketat.

4. Aktifkan Firebase Authentication dengan metode Email/Password dan Google Sign-In:

   - Di Firebase Console, pilih Authentication > Sign-in method
   - Aktifkan Email/Password
   - Aktifkan Google dan konfigurasi OAuth consent screen

5. Konfigurasi Firebase Cloud Messaging:
   - Di Firebase Console, pilih Cloud Messaging
   - Untuk Android, tambahkan Server Key ke backend Laravel di `.env`:
     ```
     FCM_SERVER_KEY=your_server_key
     ```

## ⚠️ Troubleshooting

### Error Asset Manifest

Jika mengalami error "Unable to load asset: AssetManifest.bin.json":

1. Pastikan semua asset terdaftar dengan benar di `pubspec.yaml`
2. Jalankan:
   ```bash
   flutter clean
   flutter pub get
   ```
3. Restart aplikasi dengan `flutter run`

### Error Firebase Firestore: Permission Denied

Jika mendapat error "Missing or insufficient permissions":

1. Buka Firebase Console > Firestore > Rules
2. Update rules dengan mengizinkan akses selama pengembangan
3. Publish rules

### Error API: 401 Unauthorized

Periksa:

1. Token JWT yang digunakan valid
2. Header Authorization diset dengan benar:
   ```dart
   headers: {
     'Authorization': 'Bearer $token',
     'Content-Type': 'application/json',
   }
   ```
3. User memiliki izin yang cukup
4. Cek expiry time token di backend

### Error Flutter Build: Gradle Failure

1. Pastikan Android SDK dan build tools terinstal dengan benar
2. Update Gradle version di `android/gradle/wrapper/gradle-wrapper.properties`
3. Jalankan:
   ```bash
   flutter clean
   cd android
   ./gradlew clean
   cd ..
   flutter pub get
   ```

### Error Koneksi API

1. Pastikan URL server benar di `lib/sendApi/Server.dart`
2. Jika menggunakan localhost/127.0.0.1 di emulator Android, ganti dengan `10.0.2.2`
3. Jika menggunakan perangkat fisik, pastikan perangkat dan server berada dalam jaringan yang sama
4. Cek firewall dan pengaturan CORS di server

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
