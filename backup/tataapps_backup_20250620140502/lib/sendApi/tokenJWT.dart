import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:flutter/material.dart';
import 'package:TATA/sendApi/AuthManager.dart';

// DEPRECATED: This class is deprecated. Use AuthManager instead.
// This class is kept for backward compatibility but delegates all functionality to AuthManager.
class TokenJwt {
  static const String _tokenKey = 'token_key';
  static const String _emailKey = 'email_key';
  static const String _tokenExpiryKey = 'token_expiry_key';

  static final AuthManager _authManager = AuthManager();

  // Simpan token
  static Future<void> saveToken(String token) async {
    await _authManager.saveToken(token);
  }

  // Ambil token
  static Future<String?> getToken() async {
    return await _authManager.getRawToken();
  }
  
  // Get token with Bearer prefix for API requests
  static Future<String?> getAuthorizationHeader() async {
    return await _authManager.getAuthorizationHeader();
  }
  
  // Cek apakah token sudah kedaluwarsa
  static Future<bool> isTokenExpired() async {
    return await _authManager.isTokenExpired();
  }
  
  // Refresh token
  static Future<bool> refreshToken() async {
    final token = await _authManager.refreshTokenIfNeeded();
    return token != null;
  }

  // Hapus token
  static Future<void> clearToken() async {
    await _authManager.logout();
  }

  static Future<void> saveEmail(String email) async {
    await _authManager.saveEmail(email);
  }

  // Ambil email
  static Future<String?> getEmail() async {
    return await _authManager.getEmail();
  }

  // Hapus email
  static Future<void> clearEmail() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_emailKey);
  }
  
  // Helper function for min
  static int min(int a, int b) {
    return a < b ? a : b;
  }
  
  // Tampilkan dialog login yang aman
  static void showSessionExpiredDialog(BuildContext context) {
    _authManager.showSessionExpiredDialog(context);
  }
}
