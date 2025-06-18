import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class ChatService {
  final FirebaseFirestore _firestore = FirebaseFirestore.instance;
  bool _useFirebaseFallback = true;
  
  // Mengatur apakah menggunakan Firebase atau fallback API
  void setUseFallback(bool useFallback) {
    _useFirebaseFallback = !useFallback;
  }
  
  // Mencoba memberikan informasi tentang cara memperbaiki masalah Firestore permission
  Future<bool> testFirestorePermission() async {
    try {
      // Coba buat dokumen sementara untuk menguji izin
      final testDoc = await _firestore.collection('permission_test').add({
        'timestamp': Timestamp.now(),
        'test': 'Ini adalah test izin'
      });
      
      // Jika berhasil, hapus dokumen test dan return true
      await testDoc.delete();
      return true;
    } catch (e) {
      print('Firestore permission test failed: $e');
      return false;
    }
  }
  
  // Mendapatkan atau membuat chat berdasarkan order ID
  Future<String> getOrCreateChatForOrder(String userId, String orderId) async {
    if (_useFirebaseFallback) {
      try {
        // Coba gunakan Firestore terlebih dahulu
        return await _getOrCreateChatFirestore(userId, orderId);
      } catch (e) {
        print('Firebase failed, using Laravel API fallback: $e');
        // Jika gagal, gunakan Laravel API sebagai fallback
        return await _getOrCreateChatLaravelFallback(userId, orderId);
      }
    } else {
      // Langsung gunakan Laravel API jika fallback diaktifkan
      return await _getOrCreateChatLaravelFallback(userId, orderId);
    }
  }
  
  // Implementasi Firebase untuk mendapatkan atau membuat chat
  Future<String> _getOrCreateChatFirestore(String userId, String orderId) async {
    try {
      // Tes izin terlebih dahulu
      final hasPermission = await testFirestorePermission();
      if (!hasPermission) {
        throw FirebaseException(
          plugin: 'cloud_firestore',
          message: 'Permission denied. Silakan atur Firestore Rules di Firebase Console.',
          code: 'permission-denied'
        );
      }
      
      // Cek apakah sudah ada chat untuk order ini
      final existingChatQuery = await _firestore
          .collection('chats')
          .where('user_id', isEqualTo: userId)
          .where('order_reference', isEqualTo: orderId)
          .limit(1)
          .get();
      
      // Jika sudah ada, kembalikan chat ID tersebut
      if (existingChatQuery.docs.isNotEmpty) {
        return existingChatQuery.docs.first.id;
      }
      
      // Jika belum ada, cari admin yang tersedia dan buat chat baru
      final adminId = await findAvailableAdmin();
      
      // Jika adminId adalah admin_default dan tidak ada di database, buat admin default
      if (adminId == 'admin_default') {
        await _createDefaultAdminIfNotExists();
      }
      
      final chatId = await createNewChat(userId, adminId, orderRef: orderId);
      
      // Kirim pesan otomatis pembuka
      await sendMessage(
        chatId, 
        "Hai! Admin TATA siap membantu Anda terkait pesanan #$orderId", 
        'admin'
      );
      
      return chatId;
    } catch (e) {
      print('Error getting or creating chat for order: $e');
      
      // Coba upload rules Firestore jika mendapat error permission-denied
      if (e.toString().contains('permission-denied')) {
        print('Mencoba mengatasi masalah permission Firestore...');
        print('Pastikan Anda telah mengupload file firestore.rules ke Firebase Console');
        print('Atau ubah rules di console.firebase.google.com');
        print('''
Untuk mengatasi masalah ini, tambahkan aturan berikut di Firebase Console:
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /{document=**} {
      allow read, write: if true; // Hanya untuk pengembangan, jangan gunakan di production!
    }
  }
}
        ''');
      }
      
      throw e;
    }
  }
  
  // Implementasi fallback menggunakan Laravel API
  Future<String> _getOrCreateChatLaravelFallback(String userId, String orderId) async {
    try {
      final userData = await UserPreferences.getUser();
      final token = userData?['access_token'];
      
      if (token == null) {
        throw Exception('Token tidak ditemukan. Silakan login kembali.');
      }
      
      // Cek apakah chat sudah ada
      final response = await http.post(
        Server.urlLaravel('chat/get-or-create'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: json.encode({
          'user_id': userId,
          'order_id': orderId,
        }),
      );
      
      final responseData = json.decode(response.body);
      
      if (response.statusCode == 200 && responseData['status'] == 'success') {
        // Gunakan ID yang diberikan dari API Laravel
        final String chatId = responseData['data']['chat_id'].toString();
        return chatId;
      } else {
        throw Exception(responseData['message'] ?? 'Gagal membuat chat');
      }
    } catch (e) {
      print('Error using Laravel API fallback: $e');
      throw e;
    }
  }
  
  // Mendapatkan semua chat untuk user tertentu
  Stream<List<ChatModel>> getChatsForUser(String userId) {
    return _firestore
        .collection('chats')
        .where('user_id', isEqualTo: userId)
        .snapshots()
        .map((snapshot) => snapshot.docs
            .map((doc) => ChatModel.fromFirestore(doc))
            .toList());
  }
  
  // Mendapatkan semua pesan dalam chat
  Stream<List<MessageModel>> getMessagesForChat(String chatId) {
    return _firestore
        .collection('messages')
        .where('chat_id', isEqualTo: chatId)
        .orderBy('created_at')
        .snapshots()
        .map((snapshot) => snapshot.docs
            .map((doc) => MessageModel.fromFirestore(doc))
            .toList());
  }
  
  // Buat chat baru
  Future<String> createNewChat(String userId, String adminId, {String? orderRef}) async {
    final docRef = await _firestore.collection('chats').add({
      'user_id': userId,
      'admin_id': adminId,
      'order_reference': orderRef,
      'created_at': Timestamp.now(),
      'updated_at': Timestamp.now(),
      'last_message': '',
      'unread_count': 0,
    });
    
    return docRef.id;
  }
  
  // Cari admin yang tersedia untuk chat (admin dengan chat paling sedikit)
  Future<String> findAvailableAdmin() async {
    try {
      // Default admin jika tidak ada admin yang ditemukan
      String availableAdminId = 'admin_default';
      
      // Cari di collection 'admins' yang memiliki flag is_chat_admin = true
      final adminSnapshot = await _firestore
          .collection('admins')
          .where('is_chat_admin', isEqualTo: true)
          .get();
      
      if (adminSnapshot.docs.isNotEmpty) {
        // Jika ada admin, maka ambil admin pertama
        availableAdminId = adminSnapshot.docs.first.id;
        
        // Jika ada lebih dari 1 admin, cari yang paling tidak sibuk
        if (adminSnapshot.docs.length > 1) {
          // Mendapatkan jumlah chat aktif untuk setiap admin
          final Map<String, int> adminChatCounts = {};
          
          for (var adminDoc in adminSnapshot.docs) {
            final String adminId = adminDoc.id;
            
            final chatCount = await _firestore
                .collection('chats')
                .where('admin_id', isEqualTo: adminId)
                .count()
                .get();
            
            adminChatCounts[adminId] = chatCount.count ?? 0; // Tambahkan nilai default 0 jika null
          }
          
          // Cari admin dengan jumlah chat paling sedikit
          availableAdminId = adminChatCounts.entries
              .reduce((a, b) => a.value < b.value ? a : b)
              .key;
        }
      }
      
      return availableAdminId;
    } catch (e) {
      print('Error finding available admin: $e');
      return 'admin_default';
    }
  }
  
  // Membuat admin default jika belum ada
  Future<void> _createDefaultAdminIfNotExists() async {
    try {
      final adminDoc = await _firestore.collection('admins').doc('admin_default').get();
      
      if (!adminDoc.exists) {
        await _firestore.collection('admins').doc('admin_default').set({
          'name': 'Admin TATA',
          'email': 'admin@tata.id',
          'is_chat_admin': true,
          'created_at': Timestamp.now(),
        });
        print('Created default admin');
      }
    } catch (e) {
      print('Error creating default admin: $e');
    }
  }
  
  // Kirim pesan baru
  Future<void> sendMessage(String chatId, String content, String senderType) async {
    await _firestore.collection('messages').add({
      'chat_id': chatId,
      'content': content,
      'sender_type': senderType,
      'is_read': false,
      'created_at': Timestamp.now(),
    });
    
    // Update timestamp chat dan pesan terakhir
    await _firestore.collection('chats').doc(chatId).update({
      'updated_at': Timestamp.now(),
      'last_message': content,
      'unread_count': FieldValue.increment(senderType == 'user' ? 0 : 1),
    });
  }
  
  // Tandai pesan sebagai sudah dibaca
  Future<void> markMessagesAsRead(String chatId, String currentUserType) async {
    // Cari semua pesan yang belum dibaca dan bukan dari pengirim
    final querySnapshot = await _firestore
        .collection('messages')
        .where('chat_id', isEqualTo: chatId)
        .where('is_read', isEqualTo: false)
        .where('sender_type', isNotEqualTo: currentUserType)
        .get();
        
    // Update semua pesan yang didapat
    final batch = _firestore.batch();
    for (final doc in querySnapshot.docs) {
      batch.update(doc.reference, {'is_read': true});
    }
    
    // Reset counter jika user membaca pesan
    if (currentUserType == 'user') {
      await _firestore.collection('chats').doc(chatId).update({
        'unread_count': 0,
      });
    }
    
    await batch.commit();
  }
} 