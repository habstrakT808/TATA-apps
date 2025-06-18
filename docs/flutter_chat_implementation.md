# Implementasi Chat Flutter dengan Firebase Firestore

Dokumen ini menjelaskan cara mengimplementasikan fitur chat di aplikasi Flutter dan Firebase.

## Setup Firestore Database

Untuk menggunakan fitur chat dengan Firebase Firestore, ikuti langkah-langkah berikut:

1. Buat project Firebase di [Firebase Console](https://console.firebase.google.com/)
2. Tambahkan aplikasi Android dan iOS ke project Firebase
3. Aktifkan Firestore Database di Firebase Console
4. Setup aturan keamanan Firestore dengan izin yang tepat

## Konfigurasi Aturan Keamanan Firestore

Secara default, Firebase Firestore menggunakan aturan keamanan yang ketat. Untuk pengembangan, Anda dapat menggunakan aturan berikut:

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

**PENTING**: Aturan di atas mengizinkan akses publik dan TIDAK BOLEH digunakan di lingkungan produksi. Untuk produksi, gunakan aturan yang lebih ketat seperti:

```
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    // Aturan untuk koleksi chats
    match /chats/{chatId} {
      // User dapat membaca chat jika dia adalah peserta (user_id)
      // atau admin yang terkait (admin_id)
      allow read: if request.auth != null &&
                  (resource.data.user_id == request.auth.uid ||
                   resource.data.admin_id == request.auth.uid);

      // User hanya dapat membuat chat, tidak bisa update atau delete
      allow create: if request.auth != null &&
                   request.resource.data.user_id == request.auth.uid;

      // Update hanya diperbolehkan untuk field tertentu
      allow update: if request.auth != null &&
                   (resource.data.user_id == request.auth.uid ||
                    resource.data.admin_id == request.auth.uid) &&
                    request.resource.data.diff(resource.data).affectedKeys()
                      .hasOnly(['updated_at', 'last_message', 'unread_count']);
    }

    // Aturan untuk koleksi messages
    match /messages/{messageId} {
      // Fungsi helper untuk memeriksa apakah user adalah peserta chat
      function isParticipant(chatId) {
        let chat = get(/databases/$(database)/documents/chats/$(chatId)).data;
        return chat.user_id == request.auth.uid || chat.admin_id == request.auth.uid;
      }

      // User dapat membaca pesan jika dia adalah peserta chat
      allow read: if request.auth != null &&
                 isParticipant(resource.data.chat_id);

      // User dapat membuat pesan jika dia adalah peserta chat
      // dan sender_type sesuai dengan tipe user
      allow create: if request.auth != null &&
                   isParticipant(request.resource.data.chat_id) &&
                   ((request.resource.data.sender_type == 'user' &&
                     get(/databases/$(database)/documents/chats/$(request.resource.data.chat_id)).data.user_id == request.auth.uid) ||
                    (request.resource.data.sender_type == 'admin' &&
                     get(/databases/$(database)/documents/chats/$(request.resource.data.chat_id)).data.admin_id == request.auth.uid));

      // Update hanya diperbolehkan untuk field is_read
      allow update: if request.auth != null &&
                   isParticipant(resource.data.chat_id) &&
                   request.resource.data.diff(resource.data).affectedKeys().hasOnly(['is_read']);
    }

    // Aturan untuk koleksi admins
    match /admins/{adminId} {
      // Hanya admin yang bisa membaca data admin
      allow read: if request.auth != null &&
                 exists(/databases/$(database)/documents/admins/$(request.auth.uid));

      // Tidak ada yang bisa membuat atau mengubah data admin melalui client
      allow write: if false;
    }
  }
}
```

## Struktur Database

Chat menggunakan dua koleksi utama:

1. **chats** - Menyimpan informasi chat

   - **user_id**: ID pengguna
   - **admin_id**: ID admin
   - **order_reference**: ID pesanan (opsional)
   - **created_at**: Timestamp pembuatan
   - **updated_at**: Timestamp terakhir diupdate
   - **last_message**: Isi pesan terakhir
   - **unread_count**: Jumlah pesan belum dibaca

2. **messages** - Menyimpan pesan-pesan dalam chat
   - **chat_id**: ID chat dari koleksi chats
   - **content**: Isi pesan
   - **sender_type**: Tipe pengirim ('user' atau 'admin')
   - **is_read**: Status dibaca (true/false)
   - **created_at**: Timestamp pengiriman

## Troubleshooting

### Error: [cloud_firestore/permission-denied] Missing or insufficient permissions

Jika Anda mendapatkan error ini, kemungkinan besar aturan keamanan Firestore Anda terlalu ketat. Untuk mengatasi masalah ini:

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Pilih project Anda
3. Buka Firestore Database
4. Klik tab "Rules"
5. Update rules dengan aturan yang lebih fleksibel (gunakan aturan pengembangan di atas)
6. Klik "Publish"

### Error: FormatException: Unexpected token '<', "<!DOCTYPE "... is not valid JSON

Error ini terjadi ketika server mengembalikan respons HTML alih-alih JSON. Beberapa kemungkinan penyebabnya:

1. URL API tidak valid
2. Server sedang dalam maintenance
3. Ada masalah autentikasi yang menyebabkan redirect ke halaman login

Untuk mengatasi ini:

- Periksa URL API
- Pastikan token autentikasi valid
- Tambahkan header 'Accept': 'application/json' ke request API

## Alur Kerja Fitur Chat

1. User membuka halaman detail pesanan
2. User mengklik tombol "Chat dengan Admin"
3. Aplikasi mencari chat yang terkait dengan pesanan tersebut
4. Jika chat sudah ada, aplikasi membuka chat tersebut
5. Jika chat belum ada, aplikasi:
   - Mencari admin yang tersedia
   - Membuat chat baru
   - Mengirim pesan selamat datang otomatis
6. User dan admin dapat saling berkirim pesan
7. Pesan diberi tanda sudah dibaca ketika dilihat

## Implementasi Client-Side

Lihat file-file berikut untuk implementasi client-side:

- `lib/services/ChatService.dart` - Layanan untuk mengelola chat
- `lib/menu/ChatListScreen.dart` - Layar daftar chat
- `lib/menu/ChatDetailScreen.dart` - Layar detail chat
- `lib/models/ChatModel.dart` - Model data chat
