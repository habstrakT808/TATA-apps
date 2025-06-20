import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:flutter/foundation.dart';

class UserPreferences {
  static const String _userKey = 'user_data';
  static const String _fcmTokenKey = 'fcm_token';
  
  // SharedPreferences instance
  static SharedPreferences? _preferences;
  
  // Init method to initialize SharedPreferences
  static Future<void> init() async {
    try {
      _preferences = await SharedPreferences.getInstance();
      debugPrint('SharedPreferences initialized successfully');
    } catch (e) {
      debugPrint('Error initializing SharedPreferences: $e');
    }
  }

  static Future<void> saveUser(Map<String, dynamic> userData) async {
    try {
      // Validasi userData
      if (userData == null) {
        debugPrint('Warning: Attempting to save null userData');
        return;
      }
      
      // Pastikan data memiliki format yang benar
      if (!userData.containsKey('access_token')) {
        debugPrint('Warning: userData tidak mengandung access_token');
      }
      
      if (!userData.containsKey('user') && !userData.containsKey('email')) {
        debugPrint('Warning: userData tidak mengandung informasi user atau email');
      }
      
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      final userJson = jsonEncode(userData);
      debugPrint('Saving user data: ${userJson.substring(0, min(100, userJson.length))}...');
      
      await prefs.setString(_userKey, userJson);
      debugPrint('User data saved successfully');
    } catch (e) {
      debugPrint('Error saving user data: $e');
    }
  }

//PENULISAN DATA
//access_token
//token_type
//user[
//id,name,email,role
// ]
//access_token
//access_token

  static Future<Map<String, dynamic>?> getUser() async {
    try {
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      final userJson = prefs.getString(_userKey);
      
      if (userJson == null) {
        debugPrint('No user data found in SharedPreferences');
        return null;
      }
      
      if (userJson.isEmpty) {
        debugPrint('Empty user data string in SharedPreferences');
        return null;
      }
      
      try {
        final userData = jsonDecode(userJson) as Map<String, dynamic>;
        debugPrint('User data retrieved successfully');
        return userData;
      } catch (e) {
        debugPrint('Error decoding user data JSON: $e');
        
        // Coba bersihkan data yang tidak valid
        await prefs.remove(_userKey);
        debugPrint('Cleared invalid user data from SharedPreferences');
        return null;
      }
    } catch (e) {
      debugPrint('Error retrieving user data: $e');
      return null;
    }
  }

  static Future<void> removeUser() async {
    try {
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      await prefs.remove(_userKey);
      debugPrint('User data removed successfully');
    } catch (e) {
      debugPrint('Error removing user data: $e');
    }
  }

  static Future<void> updateUser(Map<String, dynamic> updatedUserData) async {
    try {
      // Dapatkan data user yang ada dan gabungkan dengan data baru
      final existingData = await getUser();
      final mergedData = existingData != null 
          ? {...existingData, ...updatedUserData} 
          : updatedUserData;
      
      await saveUser(mergedData);
      debugPrint('User data updated successfully');
    } catch (e) {
      debugPrint('Error updating user data: $e');
    }
  }

  static Future<void> saveFcmToken(String token) async {
    try {
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      await prefs.setString(_fcmTokenKey, token);
      debugPrint('FCM token saved successfully');
    } catch (e) {
      debugPrint('Error saving FCM token: $e');
    }
  }

  static Future<String?> getFcmToken() async {
    try {
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      final token = prefs.getString(_fcmTokenKey);
      
      if (token == null) {
        debugPrint('No FCM token found in SharedPreferences');
      } else {
        debugPrint('FCM token retrieved successfully');
      }
      
      return token;
    } catch (e) {
      debugPrint('Error retrieving FCM token: $e');
      return null;
    }
  }

  static Future<void> removeFcmToken() async {
    try {
      final prefs = _preferences ?? await SharedPreferences.getInstance();
      await prefs.remove(_fcmTokenKey);
      debugPrint('FCM token removed successfully');
    } catch (e) {
      debugPrint('Error removing FCM token: $e');
    }
  }
  
  // Helper method untuk min
  static int min(int a, int b) {
    return a < b ? a : b;
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
