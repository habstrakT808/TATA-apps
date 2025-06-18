import 'dart:convert';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:http/http.dart' as http;

class ChatApiService {
  // Mengambil daftar chat room untuk user
  static Future<List<ChatRoom>> getChatRooms() async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData == null) {
        return [];
      }

      final response = await http.get(
        Server.urlLaravel('chat/rooms'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${userData['access_token']}',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true && data['data'] != null) {
          final List<dynamic> rooms = data['data'];
          return rooms.map((room) => ChatRoom.fromJson(room)).toList();
        }
      }

      return [];
    } catch (e) {
      print('Error getting chat rooms: $e');
      return [];
    }
  }

  // Mengambil pesan chat untuk pesanan tertentu
  static Future<List<ChatMessage>> getChatMessages(String pesananUuid) async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData == null) {
        return [];
      }

      final response = await http.get(
        Server.urlLaravel('chat/messages/$pesananUuid'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${userData['access_token']}',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true && data['messages'] != null) {
          final List<dynamic> messages = data['messages'];
          return messages.map((msg) => ChatMessage.fromJson(msg)).toList();
        }
      }

      return [];
    } catch (e) {
      print('Error getting chat messages: $e');
      return [];
    }
  }

  // Mengirim pesan chat
  static Future<bool> sendMessage(String pesananUuid, String message) async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData == null) {
        return false;
      }

      final response = await http.post(
        Server.urlLaravel('chat/send'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${userData['access_token']}',
        },
        body: jsonEncode({
          'pesanan_uuid': pesananUuid,
          'message': message,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['success'] == true;
      }

      return false;
    } catch (e) {
      print('Error sending message: $e');
      return false;
    }
  }

  // Menandai pesan sebagai dibaca
  static Future<bool> markMessagesAsRead(String pesananUuid, List<String> messageIds) async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData == null) {
        return false;
      }

      final response = await http.post(
        Server.urlLaravel('chat/mark-read'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ${userData['access_token']}',
        },
        body: jsonEncode({
          'pesanan_uuid': pesananUuid,
          'message_ids': messageIds,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['status'] == 'success';
      }

      return false;
    } catch (e) {
      print('Error marking messages as read: $e');
      return false;
    }
  }

  // Mendapatkan detail produk untuk ditampilkan di chat
  static Future<ProductChat?> getProductDetails(String jasaId) async {
    try {
      final response = await http.get(
        Server.urlLaravel('jasa/$jasaId'),
        headers: {'Accept': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['data'] != null) {
          return ProductChat.fromJson(data['data']['jasa']);
        }
      }

      return null;
    } catch (e) {
      print('Error getting product details: $e');
      return null;
    }
  }
} 