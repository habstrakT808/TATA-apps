import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/AuthManager.dart';
import 'package:TATA/models/ChatModel.dart';

class ChatServiceApi {
  static final AuthManager _authManager = AuthManager();
  
  // Buat atau dapatkan chat room untuk pesanan
  static Future<Map<String, dynamic>> createOrGetChatRoom(String orderId) async {
    try {
      final authHeader = await _authManager.getAuthorizationHeader();
      if (authHeader == null) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('mobile/chat/create');
      final response = await http.post(
        uri,
        headers: {
          'Authorization': authHeader,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: jsonEncode({
          'pesanan_uuid': orderId,
        }),
      );
      
      debugPrint('Create chat response: ${response.statusCode} - ${response.body}');
      final responseData = jsonDecode(response.body);
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Chat room created/retrieved successfully',
          'data': responseData['data'],
          'code': response.statusCode
        };
      } else if (response.statusCode == 401) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal membuat chat room',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error creating chat room: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
  
  // Dapatkan daftar chat
  static Future<Map<String, dynamic>> getChatList() async {
    try {
      final authHeader = await _authManager.getAuthorizationHeader();
      if (authHeader == null) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('mobile/chat/list');
      final response = await http.get(
        uri,
        headers: {
          'Authorization': authHeader,
          'Accept': 'application/json',
        },
      );
      
      final responseData = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Chat list retrieved successfully',
          'data': responseData['data'],
          'code': 200
        };
      } else if (response.statusCode == 401) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal mendapatkan daftar chat',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error getting chat list: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
  
  // Kirim pesan
  static Future<Map<String, dynamic>> sendMessage(
    String chatUuid,
    String message,
    String messageType,
    {String? fileUrl}
  ) async {
    try {
      final authHeader = await _authManager.getAuthorizationHeader();
      if (authHeader == null) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('mobile/chat/send');
      final response = await http.post(
        uri,
        headers: {
          'Authorization': authHeader,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: jsonEncode({
          'chat_uuid': chatUuid,
          'message': message,
          'message_type': messageType,
          'file_url': fileUrl,
        }),
      );
      
      final responseData = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Message sent successfully',
          'data': responseData['data'],
          'code': 200
        };
      } else if (response.statusCode == 401) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal mengirim pesan',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error sending message: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
  
  // Dapatkan pesan untuk chat tertentu
  static Future<Map<String, dynamic>> getMessages(String chatUuid, {int page = 1}) async {
    try {
      final authHeader = await _authManager.getAuthorizationHeader();
      if (authHeader == null) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('mobile/chat/messages?chat_uuid=$chatUuid&page=$page');
      final response = await http.get(
        uri,
        headers: {
          'Authorization': authHeader,
          'Accept': 'application/json',
        },
      );
      
      final responseData = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Messages retrieved successfully',
          'data': responseData['data'],
          'code': 200
        };
      } else if (response.statusCode == 401) {
        return {
          'status': 'error',
          'message': 'Token tidak valid: Anda perlu login kembali',
          'code': 401
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal mendapatkan pesan',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error getting messages: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
} 