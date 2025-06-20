import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/sendApi/AuthManager.dart';

class PesananApi {
  // Buat pesanan dengan transaksi
  static Future<Map<String, dynamic>> createPesananWithTransaction({
    required String idJasa,
    required String idPaketJasa,
    required String catatanUser,
    required String idMetodePembayaran,
    required int maksimalRevisi,
    File? gambarReferensi,
    Uint8List? webImageBytes,
  }) async {
    try {
      // Dapatkan token autentikasi dari AuthManager
      final authManager = AuthManager();
      String? token = await authManager.getAuthorizationHeader();
      
      // Jika token tidak ada, coba refresh
      if (token == null) {
        debugPrint('Token tidak ditemukan, mencoba refresh...');
        final refreshedToken = await authManager.refreshTokenIfNeeded();
        if (refreshedToken != null) {
          token = 'Bearer $refreshedToken';
          debugPrint('Token berhasil di-refresh');
        } else {
          debugPrint('Token tidak ditemukan dan gagal di-refresh');
          return {
            'status': 'error',
            'message': 'Token tidak ditemukan atau gagal di-refresh',
            'code': 401
          };
        }
      }

      // Dapatkan user data
      final userData = await UserPreferences.getUser();
      final userId = await _extractUserId(userData);
      
      if (userId == null) {
        debugPrint('User ID tidak ditemukan dalam data pengguna');
        return {
          'status': 'error',
          'message': 'Data pengguna tidak ditemukan',
          'code': 401
        };
      }
      
      // Buat request multipart untuk mendukung upload file
      final uri = Server.urlLaravel('api/mobile/pesanan/create-with-transaction');
      final request = http.MultipartRequest('POST', uri);
      
      // Set header dengan token
      request.headers['Authorization'] = token;
      request.headers['Accept'] = 'application/json';
      
      // Set fields
      request.fields['id_jasa'] = idJasa;
      request.fields['id_paket_jasa'] = idPaketJasa;
      request.fields['catatan_user'] = catatanUser;
      request.fields['maksimal_revisi'] = maksimalRevisi.toString();
      request.fields['id_metode_pembayaran'] = idMetodePembayaran;
      request.fields['user_id'] = userId.toString(); // Tambahkan user_id langsung
      
      debugPrint('Headers: ${request.headers}');
      debugPrint('Fields: ${request.fields}');
      debugPrint('URL: ${uri.toString()}');
      
      // Tambahkan file jika ada
      if (gambarReferensi != null) {
        final file = await http.MultipartFile.fromPath(
          'gambar_referensi', 
          gambarReferensi.path,
          contentType: MediaType('image', 'jpeg')
        );
        request.files.add(file);
      } else if (webImageBytes != null) {
        final file = http.MultipartFile.fromBytes(
          'gambar_referensi',
          webImageBytes,
          filename: 'web_image.jpg',
          contentType: MediaType('image', 'jpeg')
        );
        request.files.add(file);
      }
      
      // Kirim request
      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      
      debugPrint('Response status: ${response.statusCode}');
      debugPrint('Response body: ${response.body}');
      
      // Jika 401, coba refresh token dan kirim ulang
      if (response.statusCode == 401) {
        debugPrint('Mendapatkan 401, mencoba refresh token...');
        final refreshedToken = await authManager.refreshTokenIfNeeded();
        
        if (refreshedToken != null) {
          // Buat request baru dengan token yang sudah di-refresh
          debugPrint('Token berhasil di-refresh, mengirim ulang request...');
          
          final newRequest = http.MultipartRequest('POST', uri);
          newRequest.headers['Authorization'] = 'Bearer $refreshedToken';
          newRequest.headers['Accept'] = 'application/json';
          newRequest.fields.addAll(request.fields);
          
          // Tambahkan file jika ada
          if (gambarReferensi != null) {
            final file = await http.MultipartFile.fromPath(
              'gambar_referensi', 
              gambarReferensi.path,
              contentType: MediaType('image', 'jpeg')
            );
            newRequest.files.add(file);
          } else if (webImageBytes != null) {
            final file = http.MultipartFile.fromBytes(
              'gambar_referensi',
              webImageBytes,
              filename: 'web_image.jpg',
              contentType: MediaType('image', 'jpeg')
            );
            newRequest.files.add(file);
          }
          
          // Kirim request baru
          final newStreamedResponse = await newRequest.send();
          final newResponse = await http.Response.fromStream(newStreamedResponse);
          
          debugPrint('New response status: ${newResponse.statusCode}');
          debugPrint('New response body: ${newResponse.body}');
          
          // Parse response baru
          Map<String, dynamic> newResponseData;
          try {
            newResponseData = jsonDecode(newResponse.body);
          } catch (e) {
            debugPrint('Error parsing new response: $e');
            newResponseData = {
              'message': 'Format respons tidak valid: ${newResponse.body}',
              'data': null
            };
          }
          
          if (newResponse.statusCode == 201 || newResponse.statusCode == 200) {
            return {
              'status': 'success',
              'message': newResponseData['message'] ?? 'Pesanan berhasil dibuat',
              'data': newResponseData['data'] ?? {'id_pesanan': 'temp-${DateTime.now().millisecondsSinceEpoch}'},
              'code': newResponse.statusCode
            };
          } else {
            return {
              'status': 'error',
              'message': newResponseData['message'] ?? 'Gagal membuat pesanan: ${newResponse.statusCode}',
              'code': newResponse.statusCode
            };
          }
        }
      }
      
      // Parse response
      Map<String, dynamic> responseData;
      try {
        responseData = jsonDecode(response.body);
      } catch (e) {
        debugPrint('Error parsing response: $e');
        responseData = {
          'message': 'Format respons tidak valid: ${response.body}',
          'data': null
        };
      }
      
      if (response.statusCode == 201 || response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Pesanan berhasil dibuat',
          'data': responseData['data'] ?? {'id_pesanan': 'temp-${DateTime.now().millisecondsSinceEpoch}'},
          'code': response.statusCode
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal membuat pesanan: ${response.statusCode}',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error creating order with transaction: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
  
  // Ekstrak user ID dari data pengguna
  static Future<String?> _extractUserId(Map<String, dynamic>? userData) async {
    if (userData == null) {
      debugPrint('User data is null');
      return null;
    }
    
    String? userId;
    
    // Debug untuk melihat struktur data
    debugPrint('Extracting user ID from userData: $userData');
    
    // Coba ekstrak dari struktur user.id
    if (userData.containsKey('user') && 
        userData['user'] != null && 
        userData['user'] is Map) {
      final user = userData['user'];
      if (user.containsKey('id')) {
        userId = user['id'].toString();
        debugPrint('Found user ID in userData[\'user\'][\'id\']: $userId');
        return userId;
      } else if (user.containsKey('uuid')) {
        userId = user['uuid'].toString();
        debugPrint('Found user ID in userData[\'user\'][\'uuid\']: $userId');
        return userId;
      }
    }
    
    // Coba ekstrak dari struktur data.user.id
    if (userData.containsKey('data') && 
        userData['data'] != null && 
        userData['data'] is Map) {
      final data = userData['data'];
      if (data.containsKey('user') && 
          data['user'] != null && 
          data['user'] is Map) {
        final user = data['user'];
        if (user.containsKey('id')) {
          userId = user['id'].toString();
          debugPrint('Found user ID in userData[\'data\'][\'user\'][\'id\']: $userId');
          return userId;
        } else if (user.containsKey('uuid')) {
          userId = user['uuid'].toString();
          debugPrint('Found user ID in userData[\'data\'][\'user\'][\'uuid\']: $userId');
          return userId;
        }
      } else if (data.containsKey('id')) {
        userId = data['id'].toString();
        debugPrint('Found user ID in userData[\'data\'][\'id\']: $userId');
        return userId;
      } else if (data.containsKey('uuid')) {
        userId = data['uuid'].toString();
        debugPrint('Found user ID in userData[\'data\'][\'uuid\']: $userId');
        return userId;
      }
    }
    
    // Coba ekstrak langsung dari id atau uuid
    if (userData.containsKey('id')) {
      userId = userData['id'].toString();
      debugPrint('Found user ID in userData[\'id\']: $userId');
      return userId;
    } else if (userData.containsKey('uuid')) {
      userId = userData['uuid'].toString();
      debugPrint('Found user ID in userData[\'uuid\']: $userId');
      return userId;
    }
    
    debugPrint('Could not extract user ID from userData');
    return null;
  }
  
  // Dapatkan detail pesanan
  static Future<Map<String, dynamic>> getDetailPesanan(String uuid) async {
    try {
      // Dapatkan token autentikasi
      final authManager = AuthManager();
      final token = await authManager.getAuthorizationHeader();
      
      if (token == null) {
        return {
          'status': 'error',
          'message': 'Token tidak ditemukan atau gagal di-refresh',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('api/mobile/pesanan/detail/$uuid');
      final response = await http.get(
        uri,
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        },
      );
      
      Map<String, dynamic> responseData;
      try {
        responseData = jsonDecode(response.body);
      } catch (e) {
        responseData = {'message': 'Format respons tidak valid'};
      }
      
      if (response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Data berhasil diambil',
          'data': responseData['data'],
          'code': 200
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal mendapatkan detail pesanan',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error getting order detail: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
  
  // Dapatkan semua pesanan
  static Future<Map<String, dynamic>> getAllPesanan() async {
    try {
      // Dapatkan token autentikasi
      final authManager = AuthManager();
      final token = await authManager.getAuthorizationHeader();
      
      if (token == null) {
        return {
          'status': 'error',
          'message': 'Token tidak ditemukan atau gagal di-refresh',
          'code': 401
        };
      }
      
      final uri = Server.urlLaravel('api/mobile/pesanan');
      final response = await http.get(
        uri,
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        },
      );
      
      Map<String, dynamic> responseData;
      try {
        responseData = jsonDecode(response.body);
      } catch (e) {
        responseData = {'message': 'Format respons tidak valid'};
      }
      
      if (response.statusCode == 200) {
        return {
          'status': 'success',
          'message': responseData['message'] ?? 'Data berhasil diambil',
          'data': responseData['data'],
          'code': 200
        };
      } else {
        return {
          'status': 'error',
          'message': responseData['message'] ?? 'Gagal mendapatkan daftar pesanan',
          'code': response.statusCode
        };
      }
    } catch (e) {
      debugPrint('Error getting all orders: $e');
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e',
        'code': 500
      };
    }
  }
} 