import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/BeforeLogin/page_login.dart';

class AuthManager {
  static const String _tokenKey = 'token_key';
  static const String _emailKey = 'email_key';
  static const String _tokenExpiryKey = 'token_expiry_key';
  static const int _tokenCacheExpiryMinutes = 1; // Cache valid for 1 minute
  
  // Singleton pattern
  static final AuthManager _instance = AuthManager._internal();
  factory AuthManager() => _instance;
  
  // Memory cache untuk mengurangi akses ke SharedPreferences
  String? _cachedToken;
  DateTime? _cachedTokenTime;
  
  AuthManager._internal();
  
  // Get token from SharedPreferences
  Future<String?> getRawToken() async {
    try {
      // Cek cache terlebih dahulu
      if (_cachedToken != null && _cachedTokenTime != null) {
        final cacheAge = DateTime.now().difference(_cachedTokenTime!);
        if (cacheAge.inMinutes < _tokenCacheExpiryMinutes) {
          debugPrint('Using cached token');
          return _cachedToken;
        }
      }
      
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      
      if (token != null && token.isNotEmpty) {
        // Update cache
        _cachedToken = token;
        _cachedTokenTime = DateTime.now();
        debugPrint('Token retrieved from SharedPreferences and cached');
      } else {
        debugPrint('No token found in SharedPreferences');
      }
      
      return token;
    } catch (e) {
      debugPrint('Error in getRawToken: $e');
      return null;
    }
  }
  
  // Get authorization header with Bearer prefix  
  Future<String?> getAuthorizationHeader() async {
    try {
      final token = await getRawToken();
      if (token == null || token.isEmpty) {
        debugPrint('No token found, trying to refresh...');
        final refreshed = await refreshTokenIfNeeded();
        if (refreshed == null) {
          debugPrint('Token refresh failed');
          return null;
        }
        return 'Bearer $refreshed';
      }
      
      // Check if token already has Bearer prefix
      if (token.startsWith('Bearer ')) {
        return token;
      } else {
        return 'Bearer $token';
      }
    } catch (e) {
      debugPrint('Error in getAuthorizationHeader: $e');
      return null;
    }
  }
  
  // Save token to SharedPreferences
  Future<void> saveToken(String token) async {
    try {
      // Remove Bearer prefix if it exists
      String cleanToken = token;
      if (token.startsWith('Bearer ')) {
        cleanToken = token.substring('Bearer '.length);
      }
      
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', cleanToken);
      
      // Update cache
      _cachedToken = cleanToken;
      _cachedTokenTime = DateTime.now();
      
      // Set expiry time - 24 hours from now
      final expiryTime = DateTime.now().add(Duration(hours: 24)).millisecondsSinceEpoch;
      await prefs.setInt(_tokenExpiryKey, expiryTime);
      
      debugPrint('Token saved to SharedPreferences and cached');
    } catch (e) {
      debugPrint('Error in saveToken: $e');
    }
  }
  
  // Simpan email
  Future<void> saveEmail(String email) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_emailKey, email);
      debugPrint('Email saved');
    } catch (e) {
      debugPrint('Error in saveEmail: $e');
    }
  }
  
  // Get email
  Future<String?> getEmail() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString(_emailKey);
    } catch (e) {
      debugPrint('Error in getEmail: $e');
      return null;
    }
  }
  
  // Cek apakah token sudah kedaluwarsa
  Future<bool> isTokenExpired() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final expiry = prefs.getInt(_tokenExpiryKey);
      
      if (expiry == null) {
        debugPrint('No token expiry found');
        return true;
      }
      
      final now = DateTime.now().millisecondsSinceEpoch;
      final isExpired = now > expiry;
      
      if (isExpired) {
        debugPrint('Token has expired');
      }
      
      return isExpired;
    } catch (e) {
      debugPrint('Error in isTokenExpired: $e');
      return true;
    }
  }
  
  // Refresh token if needed 
  Future<String?> refreshTokenIfNeeded() async {
    try {
      final token = await getRawToken();
      if (token == null || token.isEmpty) {
        debugPrint('No token to refresh');
        return null;
      }
      
      // Always try to refresh token regardless of expiry time
      // This is to handle cases where token is rejected by server but not expired locally
      debugPrint('Attempting to refresh token...');
      
      // Try to get user data from SharedPreferences
      final userData = await UserPreferences.getUser();
      final email = await getEmail();
      
      if (userData == null) {
        debugPrint('No user data available for token refresh');
        return null;
      }
      
      // Extract email from user data if not directly available
      final userEmail = email ?? _extractEmail(userData);
      
      if (userEmail == null || userEmail.isEmpty) {
        debugPrint('No email found for token refresh');
        return null;
      }
      
      // Make a request to refresh token API
      try {
        // Use the correct endpoint for token refresh
        final response = await http.post(
          Server.urlLaravel('api/mobile/users/refresh-token'),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $token'
          },
          body: json.encode({
            'email': userEmail,
          }),
        );
        
        debugPrint('Token refresh response status: ${response.statusCode}');
        
        if (response.statusCode == 200) {
          final responseData = json.decode(response.body);
          String? newToken;
          
          // Handle different response structures
          if (responseData.containsKey('access_token')) {
            newToken = responseData['access_token'];
          } else if (responseData.containsKey('data') && 
                    responseData['data'] is Map && 
                    responseData['data'].containsKey('access_token')) {
            newToken = responseData['data']['access_token'];
          }
          
          if (newToken != null && newToken.isNotEmpty) {
            await saveToken(newToken);
            
            // Update user data with new token if needed
            if (userData.containsKey('access_token')) {
              userData['access_token'] = newToken;
              await UserPreferences.saveUser(userData);
            }
            
            debugPrint('Token refreshed successfully');
            return newToken;
          } else {
            debugPrint('Token refresh response did not contain a new token');
          }
        } else {
          debugPrint('Failed to refresh token: ${response.statusCode} - ${response.body}');
        }
      } catch (e) {
        debugPrint('Error during token refresh: $e');
      }
      
      // If we reached here, token refresh failed
      return null;
    } catch (e) {
      debugPrint('Error in refreshTokenIfNeeded: $e');
      return null;
    }
  }
  
  // Extract email from user data
  String? _extractEmail(Map<String, dynamic> userData) {
    if (userData.containsKey('user') && 
        userData['user'] != null && 
        userData['user'] is Map && 
        userData['user'].containsKey('email')) {
      return userData['user']['email'];
    } else if (userData.containsKey('data') && 
              userData['data'] != null && 
              userData['data'] is Map) {
      final dataObject = userData['data'];
      if (dataObject.containsKey('user') && 
          dataObject['user'] != null && 
          dataObject['user'] is Map && 
          dataObject['user'].containsKey('email')) {
        return dataObject['user']['email'];
      }
    } else if (userData.containsKey('email')) {
      return userData['email'];
    }
    
    return null;
  }
  
  // Verify token with server - useful for checking token validity
  Future<bool> verifyToken() async {
    try {
      final token = await getAuthorizationHeader();
      if (token == null) {
        debugPrint('No token to verify');
        return false;
      }
      
      final response = await http.get(
        Server.urlLaravel('users/verify-token'),
        headers: {'Authorization': token},
      );
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data['valid'] == true;
      }
      
      return false;
    } catch (e) {
      debugPrint('Error verifying token: $e');
      return false;
    }
  }
  
  // Handle API responses for 401 errors
  Future<bool> handleApiResponse(http.Response response, BuildContext? context) async {
    if (response.statusCode == 401) {
      debugPrint('Received 401 Unauthorized response');
      
      // Try to parse response for more details
      try {
        final data = json.decode(response.body);
        final message = data['message'] ?? 'Unauthorized';
        debugPrint('Auth error: $message');
        
        // If token is expired or invalid and context is provided
        if (context != null && (message.contains('expired') || message.contains('invalid') || message.contains('Unauthenticated'))) {
          // Try to refresh token first
          final refreshedToken = await refreshTokenIfNeeded();
          if (refreshedToken != null) {
            debugPrint('Token refreshed successfully on 401');
            return true; // Token refreshed, retry the request
          }
          
          // If refresh failed, show session expired dialog
          debugPrint('Failed to refresh token on 401, showing session expired dialog');
          showSessionExpiredDialog(context);
          return false;
        }
      } catch (e) {
        debugPrint('Error parsing 401 response: $e');
      }
      
      return false;
    }
    
    return true; // No auth error
  }
  
  // Clear token
  Future<void> logout() async {
    try {
      // Clear cache
      _cachedToken = null;
      _cachedTokenTime = null;
      
      // Clear SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('token');
      await prefs.remove(_tokenExpiryKey);
      await prefs.remove(_emailKey);
      await UserPreferences.removeUser();
      
      debugPrint('Token & user data cleared');
    } catch (e) {
      debugPrint('Error in logout: $e');
    }
  }
  
  // Force log out and redirect to login screen
  void forceLogout(BuildContext context) {
    logout().then((_) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (context) => const page_login()),
        (route) => false,
      );
    });
  }
  
  // Show session expired dialog
  void showSessionExpiredDialog(BuildContext context) {
    // Prevent multiple dialogs
    if (ModalRoute.of(context)?.isCurrent != true) {
      debugPrint('Not showing session expired dialog - not on current route');
      return;
    }
    
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text("Sesi Berakhir"),
          content: const Text("Sesi login Anda telah berakhir. Silakan login kembali untuk melanjutkan."),
          actions: [
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF198754),
                foregroundColor: Colors.white,
              ),
              child: const Text("Login"),
              onPressed: () {
                // Hapus token dan navigasi ke halaman login
                logout().then((_) {
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(builder: (context) => const page_login()),
                    (route) => false,
                  );
                });
              },
            ),
          ],
        );
      },
    );
  }
  
  // Get user ID from saved user data
  Future<String?> getUserId() async {
    final userData = await UserPreferences.getUser();
    if (userData != null && userData.containsKey('user') && userData['user'] != null) {
      if (userData['user'] is Map && userData['user'].containsKey('id')) {
        return userData['user']['id'].toString();
      }
    } else if (userData != null && userData.containsKey('data') && userData['data'] != null) {
      if (userData['data'] is Map && 
          userData['data'].containsKey('user') && 
          userData['data']['user'] != null && 
          userData['data']['user'] is Map && 
          userData['data']['user'].containsKey('id')) {
        return userData['data']['user']['id'].toString();
      }
    }
    return null;
  }
  
  // Check if user is logged in
  Future<bool> isLoggedIn() async {
    final token = await getRawToken();
    return token != null && token.isNotEmpty;
  }
  
  // Check if user is logged in and has valid token
  Future<bool> isLoggedInWithValidToken() async {
    try {
      // First check if user is logged in
      if (!await isLoggedIn()) {
        debugPrint('User is not logged in');
        return false;
      }
      
      // Then check if token is expired and try to refresh if needed
      final refreshedToken = await refreshTokenIfNeeded();
      if (refreshedToken == null || refreshedToken.isEmpty) {
        debugPrint('Token refresh failed or token is invalid');
        return false;
      }
      
      // If we got here, user is logged in with valid token
      return true;
    } catch (e) {
      debugPrint('Error checking login status: $e');
      return false;
    }
  }
  
  // Handle session expiration globally
  void handleSessionExpiration(BuildContext? context) async {
    if (context != null && context.mounted) {
      // Check if current route is already login page to avoid loops
      bool isOnLoginPage = false;
      Navigator.of(context).popUntil((route) {
        isOnLoginPage = route.settings.name == '/login';
        return true;
      });
      
      if (!isOnLoginPage) {
        showSessionExpiredDialog(context);
      }
    }
    
    // Clear user data regardless of context
    await logout();
  }
} 