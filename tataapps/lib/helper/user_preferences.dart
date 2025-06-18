import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class UserPreferences {
  static const String _userKey = 'user_data';
  static const String _fcmTokenKey = 'fcm_token';
  
  // SharedPreferences instance
  static SharedPreferences? _preferences;
  
  // Init method to initialize SharedPreferences
  static Future<void> init() async {
    _preferences = await SharedPreferences.getInstance();
  }

  static Future<void> saveUser(Map<String, dynamic> userData) async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    final userJson = jsonEncode(userData);
    await prefs.setString(_userKey, userJson);
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
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      return jsonDecode(userJson);
    }
    return null;
  }

  static Future<void> removeUser() async {
    final prefs = _preferences ?? await SharedPreferences.getInstance();
    await prefs.remove(_userKey);
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
