import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/sendApi/Server.dart';

// Kelas ini dibuat untuk kompatibilitas dengan kode yang mengandalkan ChatApiService lama
class ChatApiService {
  // Get product details
  static Future<ProductChat?> getProductDetails(String jasaId) async {
    try {
      final response = await http.get(
        Server.urlLaravel('jasa/$jasaId'),
        headers: {'Accept': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['data'] != null) {
          final jasa = data['data']['jasa'];
          final title = jasa['kategori'] ?? 'Jasa';
          final category = jasa['kategori'] ?? '';
          final packageType = 'basic';
          final price = '50000'; // Default price
          
          return ProductChat(
            id: jasaId,
            title: title,
            category: category,
            price: price,
            imageUrl: '',
            packageType: packageType
          );
        }
      }
      
      debugPrint('Error getting product details: ${response.statusCode} - ${response.body}');
      return null;
    } catch (e) {
      debugPrint('Error getting product details: $e');
      return null;
    }
  }
  
  // Get chat rooms
  static Future<List<ChatRoom>> getChatRooms() async {
    try {
      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']?['id'];
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan');
        return [];
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/chat/list'),
        headers: {
          'Accept': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['data'] != null) {
          final List<dynamic> chatList = data['data'];
          return chatList.map((chat) {
            return ChatRoom(
              id: chat['uuid'] ?? '',
              pesananId: chat['pesanan_uuid'] ?? '',
              product: ProductChat(
                id: '',
                title: chat['jasa_name'] ?? 'Pesanan',
                category: chat['jasa_category'] ?? '',
                price: chat['price'] != null ? chat['price'].toString() : '0',
                imageUrl: '',
                packageType: chat['package_type'] ?? 'basic'
              ),
              lastMessage: chat['last_message'] ?? '',
              lastSender: chat['last_sender_type'] ?? 'system',
              lastTimestamp: chat['updated_at'] ?? DateTime.now().toIso8601String()
            );
          }).toList();
        }
      }
      
      debugPrint('Error getting chat rooms: ${response.statusCode} - ${response.body}');
      return [];
    } catch (e) {
      debugPrint('Error getting chat rooms: $e');
      return [];
    }
  }
  
  // Get chat messages
  static Future<List<ChatMessage>> getChatMessages(String pesananUuid) async {
    try {
      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']?['id'];
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan');
        return _getDefaultMessages();
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/chat/messages-by-pesanan/$pesananUuid'),
        headers: {
          'Accept': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['messages'] != null) {
          final List<dynamic> messageList = data['messages'];
          return messageList.map((msg) => ChatMessage.fromJson(msg)).toList();
        } else if (response.statusCode == 404) {
          // Try to create the chat first if not found
          debugPrint('Chat not found, attempting to create it first');
          await getOrCreateChatForOrder(pesananUuid);
          // Try again after creating
          return getChatMessages(pesananUuid);
        }
      }
      
      return _getDefaultMessages();
    } catch (e) {
      debugPrint('Error getting chat messages: $e');
      return _getDefaultMessages();
    }
  }
  
  // Helper method untuk mendapatkan pesan default
  static List<ChatMessage> _getDefaultMessages() {
    return [
      ChatMessage(
        id: '1',
        text: "Selamat! Pesanan Anda telah diterima. Admin akan segera menghubungi Anda untuk informasi lebih lanjut.",
        sender: 'admin',
        timestamp: DateTime.now().toIso8601String(),
        isRead: true,
      )
    ];
  }
  
  // Send message
  static Future<bool> sendMessage(String pesananUuid, String message, {String? fileUrl}) async {
    try {
      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']?['id'];
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan');
        return false;
      }
      
      // First try to get or create a chat for this order to ensure it exists
      try {
        await getOrCreateChatForOrder(pesananUuid);
      } catch (e) {
        debugPrint('Error getting or creating chat: $e');
        // Continue anyway, as the chat might already exist
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/send-by-pesanan'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'pesanan_uuid': pesananUuid,
          'message': message,
          'message_type': fileUrl != null ? 'image' : 'text',
          'file_url': fileUrl,
          'user_id': userId,
        }),
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['status'] == 'success';
      } else if (response.statusCode == 404) {
        // Special handling for 404 - try to create the chat first
        debugPrint('Chat not found, attempting to create it first');
        final chatCreated = await getOrCreateChatForOrder(pesananUuid);
        if (chatCreated != null) {
          // Try sending the message again
          return sendMessage(pesananUuid, message, fileUrl: fileUrl);
        }
      }
      
      return false;
    } catch (e) {
      debugPrint('Error sending message: $e');
      return false;
    }
  }
  
  // Mark messages as read
  static Future<bool> markMessagesAsRead(String chatUuid) async {
    try {
      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']?['id'];
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan');
        return false;
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/mark-read'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'chat_uuid': chatUuid,
          'user_id': userId,
        }),
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['status'] == 'success';
      }
      
      return false;
    } catch (e) {
      debugPrint('Error marking messages as read: $e');
      return false;
    }
  }
  
  // Get or create chat for order
  static Future<Map<String, dynamic>?> getOrCreateChatForOrder(String pesananUuid) async {
    try {
      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']?['id'];
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan');
        return null;
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/get-or-create'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'pesanan_uuid': pesananUuid,
          'user_id': userId,
        }),
      );
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['data'] != null) {
          return {
            'chat': data['data'],
            'messages': data['messages'] ?? [],
          };
        }
      }
      
      return null;
    } catch (e) {
      debugPrint('Error getting or creating chat for order: $e');
      return null;
    }
  }
  
  // Metode untuk membuat chat room
  static Future<bool> createOrGetChatRoom(String pesananUuid) async {
    try {
      final result = await getOrCreateChatForOrder(pesananUuid);
      return result != null;
    } catch (e) {
      debugPrint('Error creating chat room: $e');
      return false;
    }
  }
  
  // Helper method untuk mendapatkan authenticated HTTP client dengan token refresh handling
  static Future<Map<String, String>> getAuthHeaders() async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData == null || !userData.containsKey('access_token')) {
        debugPrint('No access token available in UserPreferences');
        return {'Accept': 'application/json'};
      }
      
      final token = userData['access_token'];
      return {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };
    } catch (e) {
      debugPrint('Error getting auth headers: $e');
      return {'Accept': 'application/json'};
    }
  }
  
  // Metode untuk authenticated GET request dengan token refresh
  static Future<http.Response> authenticatedGet(Uri url) async {
    try {
      final headers = await getAuthHeaders();
      return await http.get(url, headers: headers);
    } catch (e) {
      debugPrint('Error making authenticated GET request: $e');
      throw e;
    }
  }
  
  // Metode untuk authenticated POST request dengan token refresh
  static Future<http.Response> authenticatedPost(Uri url, {Object? body}) async {
    try {
      final headers = await getAuthHeaders();
      headers['Content-Type'] = 'application/json';
      
      return await http.post(
        url, 
        headers: headers,
        body: body is String ? body : jsonEncode(body),
      );
    } catch (e) {
      debugPrint('Error making authenticated POST request: $e');
      throw e;
    }
  }
} 