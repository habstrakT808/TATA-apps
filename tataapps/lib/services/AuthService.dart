import 'package:firebase_auth/firebase_auth.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/helper/fcm_helper.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/tokenJWT.dart';
import 'package:flutter/material.dart';
import 'package:TATA/main.dart'; // Import untuk navigatorKey

class AuthService {
  final FirebaseAuth _auth = FirebaseAuth.instance;
  final GoogleSignIn _googleSignIn = GoogleSignIn(
    clientId: kIsWeb ? '244660030535-lodn72p8i6c0e8ufni92k9lorfqsrffc.apps.googleusercontent.com' : null,
  );
  
  // Stream untuk status autentikasi
  Stream<User?> get authStateChanges => _auth.authStateChanges();
  
  // Mendapatkan user saat ini
  User? get currentUser => _auth.currentUser;
  
  // Login dengan email dan password
  Future<UserCredential> signInWithEmailPassword(String email, String password) async {
    try {
      // Login ke Firebase
      final credential = await _auth.signInWithEmailAndPassword(
        email: email,
        password: password
      );
      
      // Jika berhasil, login ke backend Laravel
      await _signInToLaravel(email, password);
      
      // Update FCM token
      await FCMHelper().updateFCMToken();
      
      return credential;
    } catch (e) {
      debugPrint('Error signing in with email/password: $e');
      rethrow;
    }
  }
  
  // Registrasi dengan email dan password
  Future<UserCredential> registerWithEmailPassword(String name, String email, String password) async {
    try {
      // Registrasi ke Firebase
      final credential = await _auth.createUserWithEmailAndPassword(
        email: email,
        password: password
      );
      
      // Update display name
      await credential.user?.updateDisplayName(name);
      
      // Registrasi ke backend Laravel
      await _registerToLaravel(name, email, password);
      
      // Update FCM token (tangani error jika ada)
      try {
        await FCMHelper().updateFCMToken();
      } catch (e) {
        debugPrint('Error updating FCM token during registration: $e');
        // Lanjutkan proses registrasi meskipun FCM gagal
      }
      
      return credential;
    } catch (e) {
      debugPrint('Error registering with email/password: $e');
      rethrow;
    }
  }
  
  // Login dengan Google
  Future<UserCredential> signInWithGoogle() async {
    try {
      // Trigger the authentication flow
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
      
      if (googleUser == null) {
        throw Exception('Google sign in aborted');
      }
      
      // Obtain the auth details from the request
      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
      
      // Create a new credential
      final credential = GoogleAuthProvider.credential(
        accessToken: googleAuth.accessToken,
        idToken: googleAuth.idToken,
      );
      
      // Sign in to Firebase with the credential
      final userCredential = await _auth.signInWithCredential(credential);
      
      // Login atau registrasi ke backend Laravel
      await _signInOrRegisterWithGoogleToLaravel(
        userCredential.user?.displayName ?? '',
        userCredential.user?.email ?? '',
        googleAuth.idToken ?? ''
      );
      
      // Update FCM token
      await FCMHelper().updateFCMToken();
      
      return userCredential;
    } catch (e) {
      debugPrint('Error signing in with Google: $e');
      rethrow;
    }
  }
  
  // Logout
  Future<void> signOut() async {
    try {
      // Logout dari backend Laravel
      await _signOutFromLaravel();
      
      // Logout dari Google jika digunakan
      await _googleSignIn.signOut();
      
      // Logout dari Firebase
      await _auth.signOut();
      
      // Hapus data user lokal
      await UserPreferences.clearUser();
    } catch (e) {
      debugPrint('Error signing out: $e');
      rethrow;
    }
  }
  
  // Reset password
  Future<void> resetPassword(String email) async {
    try {
      await _auth.sendPasswordResetEmail(email: email);
    } catch (e) {
      debugPrint('Error resetting password: $e');
      rethrow;
    }
  }
  
  // Verifikasi email
  Future<void> sendEmailVerification() async {
    try {
      await _auth.currentUser?.sendEmailVerification();
    } catch (e) {
      debugPrint('Error sending email verification: $e');
      rethrow;
    }
  }
  
  // Login ke backend Laravel
  Future<void> _signInToLaravel(String email, String password) async {
    try {
      debugPrint('Login to Laravel: $email');
      final response = await http.post(
        Server.urlLaravel('mobile/users/login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      );
      
      debugPrint('Login response status: ${response.statusCode}');
      debugPrint('Login response body: ${response.body}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        debugPrint('Login response: $data');
        
        // Reset token terlebih dahulu
        await UserPreferences.clearToken();
        await TokenJwt.clearToken();
        
        // Pastikan struktur data konsisten
        String token = '';
        
        // Cek struktur data yang benar
        if (data.containsKey('data') && data['data'].containsKey('access_token')) {
          token = data['data']['access_token'];
          debugPrint('Token from data.access_token: $token');
        } else if (data.containsKey('access_token')) {
          token = data['access_token'];
          debugPrint('Token from access_token: $token');
        }
        
        // Simpan token dengan format Bearer
        if (token.isNotEmpty) {
          if (!token.startsWith('Bearer ')) {
            token = 'Bearer $token';
          }
          debugPrint('Saving token: $token');
          await UserPreferences.saveToken(token);
          await TokenJwt.saveToken(token);
        } else {
          debugPrint('Warning: No token found in response');
        }
        
        // Simpan data user
        await UserPreferences.saveUser(data);
        
        // Update FCM token setelah login berhasil
        await FCMHelper().updateFCMToken();
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Failed to login to server');
      }
    } catch (e) {
      debugPrint('Error signing in to Laravel: $e');
      rethrow;
    }
  }
  
  // Fungsi untuk refresh token
  Future<bool> refreshToken() async {
    try {
      debugPrint('Attempting to refresh token');
      final currentToken = await UserPreferences.getToken();
      
      if (currentToken == null || currentToken.isEmpty) {
        debugPrint('No token available for refresh');
        return false;
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/users/refresh-token'),
        headers: {
          'Authorization': currentToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      
      debugPrint('Refresh token response status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        debugPrint('Refresh token response: $data');
        
        String token = '';
        if (data.containsKey('access_token')) {
          token = data['access_token'];
        } else if (data.containsKey('data') && data['data'].containsKey('access_token')) {
          token = data['data']['access_token'];
        }
        
        if (token.isNotEmpty) {
          if (!token.startsWith('Bearer ')) {
            token = 'Bearer $token';
          }
          
          // Simpan token baru
          await UserPreferences.saveToken(token);
          await TokenJwt.saveToken(token);
          
          debugPrint('Token refreshed successfully');
          return true;
        }
      }
      
      debugPrint('Failed to refresh token');
      return false;
    } catch (e) {
      debugPrint('Error refreshing token: $e');
      return false;
    }
  }
  
  // Fungsi untuk verifikasi token
  Future<bool> verifyToken() async {
    try {
      final token = await UserPreferences.getToken();
      
      if (token == null || token.isEmpty) {
        debugPrint('No token to verify');
        return false;
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/user/profile'),
        headers: {
          'Authorization': token,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      
      debugPrint('Verify token response status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        debugPrint('Token is valid');
        return true;
      } else if (response.statusCode == 401) {
        debugPrint('Token is invalid or expired, trying to refresh');
        return await refreshToken();
      } else {
        debugPrint('Unexpected response when verifying token: ${response.statusCode}');
        if (response.statusCode == 500) {
          debugPrint('User tidak terautentikasi, mengarahkan ke halaman login');
          navigatorKey.currentState?.pushNamedAndRemoveUntil(
            '/login', 
            (route) => false
          );
        }
        return false;
      }
    } catch (e) {
      debugPrint('Error verifying token: $e');
      return false;
    }
  }
  
  // Registrasi ke backend Laravel
  Future<void> _registerToLaravel(String name, String email, String password) async {
    try {
      debugPrint('Registering to Laravel: $email');
      final response = await http.post(
        Server.urlLaravel('mobile/users/register'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'nama_user': name,
          'email': email,
          'password': password,
          'password_confirmation': password,
          'no_telpon': await UserPreferences.getPhoneNumber() ?? '', // Ambil nomor telepon dari preferences
        }),
      );
      
      debugPrint('Register response status: ${response.statusCode}');
      debugPrint('Register response body: ${response.body}');
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        debugPrint('Register response: $data');
        
        // Pastikan struktur data konsisten
        String token = '';
        if (data.containsKey('access_token')) {
          token = data['access_token'];
        } else if (data.containsKey('data') && data['data'].containsKey('access_token')) {
          token = data['data']['access_token'];
        }
        
        // Simpan token dengan format Bearer
        if (token.isNotEmpty) {
          if (!token.startsWith('Bearer ')) {
            token = 'Bearer $token';
          }
          await UserPreferences.saveToken(token);
        }
        
        // Simpan data user
        await UserPreferences.saveUser(data);
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Failed to register to server');
      }
    } catch (e) {
      debugPrint('Error registering to Laravel: $e');
      rethrow;
    }
  }
  
  // Login atau registrasi dengan Google ke backend Laravel
  Future<void> _signInOrRegisterWithGoogleToLaravel(String name, String email, String idToken) async {
    try {
      debugPrint('Google login to Laravel: $email');
      final response = await http.post(
        Server.urlLaravel('mobile/users/login-google'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'nama_user': name,
          'email': email,
          'id_token': idToken,
        }),
      );
      
      debugPrint('Google login response status: ${response.statusCode}');
      debugPrint('Google login response body: ${response.body}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        debugPrint('Google login response: $data');
        
        // Pastikan struktur data konsisten
        String token = '';
        if (data.containsKey('access_token')) {
          token = data['access_token'];
        } else if (data.containsKey('data') && data['data'].containsKey('access_token')) {
          token = data['data']['access_token'];
        }
        
        // Simpan token dengan format Bearer
        if (token.isNotEmpty) {
          if (!token.startsWith('Bearer ')) {
            token = 'Bearer $token';
          }
          await UserPreferences.saveToken(token);
        }
        
        // Simpan data user
        await UserPreferences.saveUser(data);
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Failed to authenticate with Google');
      }
    } catch (e) {
      debugPrint('Error signing in/registering with Google to Laravel: $e');
      rethrow;
    }
  }
  
  // Logout dari backend Laravel
  Future<void> _signOutFromLaravel() async {
    try {
      final token = await UserPreferences.getToken();
      
      if (token != null) {
        await http.post(
          Server.urlLaravel('user/logout'),
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token,
          },
        );
      }
    } catch (e) {
      debugPrint('Error signing out from Laravel: $e');
      // Tidak throw error karena logout dari Firebase tetap harus dilakukan
    }
  }
} 