import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:TATA/sendApi/tokenJWT.dart';
import 'package:flutter/foundation.dart';
import 'dart:math' as Math;

class UserPreferences {
  static const String _userKey = 'user_data';
  static const String _fcmTokenKey = 'fcm_token';
  static const String _tokenKey = 'auth_token';
  static const String _phoneNumberKey = 'phone_number';
  
  // SharedPreferences instance
  static SharedPreferences? _preferences;
  
  // Init method to initialize SharedPreferences
  static Future<void> init() async {
    _preferences = await SharedPreferences.getInstance();
  }

  // Fungsi untuk menyimpan nomor telepon
  static Future<void> savePhoneNumber(String phoneNumber) async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.setString(_phoneNumberKey, phoneNumber);
  }
  
  // Fungsi untuk mendapatkan nomor telepon
  static Future<String?> getPhoneNumber() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    return prefs.getString(_phoneNumberKey);
  }

  static Future<void> saveUser(Map<String, dynamic> userData) async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    final userJson = jsonEncode(userData);
    await prefs.setString(_userKey, userJson);
    
    // Extract and save token
    String? token = _extractTokenFromUserData(userData);
    if (token != null) {
      await saveToken(token);
    }
  }

  // Ekstrak token dari berbagai format data user
  static String? _extractTokenFromUserData(Map<String, dynamic> userData) {
    String? token;
    
    // Cek berbagai kemungkinan struktur data
    if (userData.containsKey('access_token') && userData['access_token'] != null) {
      token = userData['access_token'];
    } else if (userData.containsKey('data')) {
      if (userData['data'] is Map) {
        final data = userData['data'];
        if (data.containsKey('access_token') && data['access_token'] != null) {
          token = data['access_token'];
        }
      }
    } else if (userData.containsKey('token')) {
      token = userData['token'];
    }
    
    return token;
  }

  static Future<Map<String, dynamic>?> getUser() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      try {
        final decoded = jsonDecode(userJson);
        debugPrint('UserPreferences.getUser() returning: $decoded');
        return decoded;
      } catch (e) {
        debugPrint('Error decoding user data: $e');
        return null;
      }
    }
    debugPrint('UserPreferences.getUser() returning null');
    return null;
  }
  
  // Fungsi untuk menyimpan token
  static Future<void> saveToken(String token) async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    
    // Pastikan format token konsisten
    String formattedToken = token;
    if (!token.startsWith('Bearer ')) {
      formattedToken = 'Bearer $token';
    }
    
    await prefs.setString(_tokenKey, formattedToken);
    
    // Update juga di TokenJWT untuk kompatibilitas
    await TokenJwt.saveToken(formattedToken);
    
    debugPrint('Token saved successfully: ${formattedToken.substring(0, Math.min(20, formattedToken.length))}...');
  }
  
  // Mendapatkan token dari data user
  static Future<String?> getToken() async {
    try {
      // Pertama coba ambil dari _tokenKey langsung
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      final directToken = prefs.getString(_tokenKey);
      
      if (directToken != null && directToken.isNotEmpty) {
        debugPrint('Retrieved token from preferences: ${directToken.substring(0, Math.min(20, directToken.length))}...');
        // Format token sesuai kebutuhan Laravel Sanctum
        if (!directToken.startsWith('Bearer ')) {
          return 'Bearer $directToken';
        }
        return directToken;
      }
      
      // Jika tidak ada, coba ambil dari data user
      final userData = await getUser();
      if (userData != null) {
        String? token = _extractTokenFromUserData(userData);
        
        if (token != null && token.isNotEmpty) {
          // Simpan token untuk penggunaan berikutnya
          await saveToken(token);
          
          // Format token sesuai kebutuhan Laravel Sanctum
          if (!token.startsWith('Bearer ')) {
            token = 'Bearer $token';
          }
          debugPrint('Retrieved token from user data: ${token.substring(0, Math.min(20, token.length))}...');
          return token;
        }
      }
      
      // Jika masih tidak ada, coba ambil dari TokenJWT
      final tokenJwt = await TokenJwt.getToken();
      if (tokenJwt != null && tokenJwt.isNotEmpty) {
        debugPrint('Retrieved token from TokenJWT: ${tokenJwt.substring(0, Math.min(20, tokenJwt.length))}...');
      } else {
        debugPrint('No token found in any storage');
      }
      return tokenJwt;
    } catch (e) {
      debugPrint('Error getting token: $e');
      return null;
    }
  }

  static Future<void> removeUser() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.remove(_userKey);
    // Juga hapus token
    await clearToken();
  }

  static Future<void> updateUser(Map<String, dynamic> updatedUserData) async {
    await saveUser(updatedUserData);
  }

  static Future<void> saveFcmToken(String token) async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.setString(_fcmTokenKey, token);
  }

  static Future<String?> getFcmToken() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    return prefs.getString(_fcmTokenKey);
  }

  static Future<void> removeFcmToken() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.remove(_fcmTokenKey);
  }

  // Methods for FCM token (alias untuk kompatibilitas)
  static Future<void> setFCMToken(String token) async {
    await saveFcmToken(token);
  }

  static Future<String?> getFCMToken() async {
    return getFcmToken();
  }
  
  // Fungsi untuk menghapus token
  static Future<void> clearToken() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    
    // Hapus juga token dari TokenJWT
    await TokenJwt.clearToken();
    
    debugPrint('Token cleared successfully');
  }
  
  // Fungsi untuk menghapus data user
  static Future<void> clearUser() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.remove(_userKey);
    await clearToken();
    
    debugPrint('User data cleared successfully');
  }
}

// CARA UBAH PROFIL
// Future<void> updateProfile(Map<String, dynamic> newProfileData) async {
//   final response = await http.post(
//     Uri.parse('https://example.com/api/update-profile'),
//     body: newProfileData,
//   );

//   final result = jsonDecode(response.body);
//   if (result['status'] == 'success') {
//     // Update shared preferences dengan data terbaru dari server
//     await UserPreferences.updateUser(result['data']);
//     print('Profil diperbarui dan disimpan ulang di SharedPreferences');
//   } else {
//     print('Gagal update: ${result['message']}');
//   }
// }
