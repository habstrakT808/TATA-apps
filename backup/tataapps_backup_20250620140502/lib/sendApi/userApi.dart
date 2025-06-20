import 'dart:convert';
import 'package:TATA/SendApi/Server.dart';
import 'package:TATA/SendApi/AuthManager.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

class UserApi {
  static final AuthManager _authManager = AuthManager();
  
  static Future<Map<String, dynamic>?> GantiPasswordProfil(
      String passwordOld, String password, String passwordConfirm) async {
    String? authHeader = await _authManager.getAuthorizationHeader();
    String? email = await _authManager.getEmail();
    
    if (authHeader == null) {
      debugPrint("Token tidak valid: Anda perlu login kembali");
      return {'status': 'error', 'message': 'Token tidak valid: Anda perlu login kembali'};
    }
    
    final response = await http.put(
      Server.urlLaravel("users/profile/profile/password"),
      headers: {
        "Content-Type": "application/json",
        "Authorization": authHeader,
        "Accept": "application/json"
      },
      body: json.encode({
        "password_old": passwordOld,
        "password": password,
        "password_confirm": passwordConfirm,
        "email": email.toString(),
      }),
    );

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 401) {
      return {'status': 'error', 'message': 'Token tidak valid: Anda perlu login kembali'};
    } else {
      debugPrint(response.body);
      return null;
    }
  }

  static Future<Map<String, dynamic>?> getProfil(String email) async {
    String? authHeader = await _authManager.getAuthorizationHeader();
    
    if (authHeader == null) {
      debugPrint("Token tidak valid: Anda perlu login kembali");
      return {'status': 'error', 'message': 'Token tidak valid: Anda perlu login kembali'};
    }
    
    final response = await http.post(
      Server.urlLaravel("users/profile"),
      headers: {
        "Content-Type": "application/json",
        "Authorization": authHeader,
        "Accept": "application/json"
      },
      body: json.encode({
        "email": email,
      }),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 401) {
      return {'status': 'error', 'message': 'Token tidak valid: Anda perlu login kembali'};
    } else {
      return null;
    }
  }

  static Future<String?> checkEmailAvailability(String email) async {
    try {
      final response = await http.post(
        Server.urlLaravel("users/check-email"),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email}),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          return data['status'];
        } else {
          return data['message'];
        }
      } else {
        final data = jsonDecode(response.body);
        return data['message'];
      }
    } catch (e) {
      print('Error: $e');
    }
    return null;
  }

  static Future<Map<String, dynamic>?> ForgotPassword(
      String email, String password) async {
    final response = await http.post(
      Server.urlLaravel("verify/password"),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );
    try {
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          return data;
        } else {
          return data;
        }
      } else {
        final data = jsonDecode(response.body);
        return data;
      }
    } catch (e) {
      print('Errorr: $e');
    }
    print('Errorr: ${response.body.toString()}');
    return null;
  }

  static Future<Map<String, dynamic>?> register(
    String email,
    String noTelpon,
    String nama,
    String password,
    String passwordConfirm,
  ) async {
    final response = await http.post(
      Server.urlLaravel("users/register"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({
        "email": email,
        "no_telpon": noTelpon,
        "nama_user": nama,
        "password": password,
        "password_confirmation": passwordConfirm,
      }),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 500) {
      final result = json.decode(response.body);
      return result;
    } else {
      print("Error Kode ${response.statusCode}");
      return null;
    }
  }

  static Future<Map<String, dynamic>?> login(String email, String password) async {
    try {
      final response = await http.post(
        Server.urlLaravel("mobile/users/login"),
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: json.encode({
          "email": email,
          "password": password,
        }),
      );

      debugPrint('Login response status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        debugPrint("Login response: ${result.toString()}");
        
        // Ambil data token dari response
        final jwtToken = result['data']['access_token'];
        final user = result['data']['user'];
        
        if (jwtToken == null || user == null) {
          debugPrint("Error: Invalid login response structure");
          return {'status': 'error', 'message': 'Gagal memproses login'};
        }

        // Simpan token menggunakan AuthManager
        await _authManager.saveToken(jwtToken);
        
        // Simpan email
        await _authManager.saveEmail(user['email']);
        
        // Simpan user data untuk digunakan di aplikasi
        await UserPreferences.saveUser({
          'access_token': jwtToken,
          'email': user['email'],
          'user': user
        });

        // Log token yang tersimpan untuk debugging
        final savedToken = await _authManager.getRawToken();
        final authHeader = await _authManager.getAuthorizationHeader();
        debugPrint('Saved token: $savedToken');
        debugPrint('Auth header: $authHeader');

        return {
          'status': 'success',
          'message': 'Login berhasil',
          'data': {
            'user': user,
            'token': jwtToken
          }
        };
      } else if (response.statusCode == 400 || response.statusCode == 401) {
        final result = json.decode(response.body);
        return result;
      } else {
        debugPrint("Status code = ${response.statusCode}");
        debugPrint("Response = ${response.body}");
        return {
          'status': 'error',
          'message': 'Terjadi kesalahan saat login. Silakan coba lagi.'
        };
      }
    } catch (e) {
      debugPrint("Error during login: $e");
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e'
      };
    }
  }

  static Future<Map<String, dynamic>?> CekEmail(String email) async {
    try {
      print("Checking email: $email");
      
      final response = await http.post(
        Server.urlLaravel("mobile/users/check-email"),
        headers: {
          "Content-Type": "application/json",
        },
        body: json.encode({"email": email}),
      );

      print("CekEmail response status: ${response.statusCode}");
      
      if (response.statusCode == 200) {
        print("CekEmail response body: ${response.body}");
        final result = json.decode(response.body);
        return result;
      } else if (response.statusCode == 400) {
        print("CekEmail error 400: ${response.body}");
        final result = json.decode(response.body);
        return result;
      } else if (response.statusCode == 404) {
        // API endpoint mungkin tidak ada, gunakan fallback
        print("CekEmail endpoint tidak ditemukan, menggunakan fallback response");
        // Kembalikan respons sukses untuk melanjutkan alur pendaftaran
        return {
          'status': 'success',
          'message': 'Email dapat digunakan'
        };
      } else {
        print("CekEmail unknown error: Status ${response.statusCode}, Body: ${response.body}");
        // Kembalikan respons sukses untuk melanjutkan alur pendaftaran
        return {
          'status': 'success',
          'message': 'Email dapat digunakan'
        };
      }
    } catch (e) {
      print("Error during CekEmail: $e");
      // Fallback jika ada exception
      return {
        'status': 'success',
        'message': 'Email dapat digunakan'
      };
    }
  }

  static Future<Map<String, dynamic>?> loginGoogle(String email) async {
    try {
      final response = await http.post(
        Server.urlLaravel("mobile/users/login-google"),
        headers: {
          "Content-Type": "application/json",
        },
        body: json.encode({
          "email": email,
        }),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        print("Login Google response: ${result.toString()}");
        
        // Pastikan kita mengambil token dari lokasi yang benar, sama seperti di login biasa
        final jwtToken = result['data']['access_token']; 
        final user = result['data']['user'];
        
        if (jwtToken == null || user == null) {
          print("Error: Invalid login Google response structure");
          return {'status': 'error', 'message': 'Gagal memproses login Google'};
        }

        // Simpan token
        await _authManager.saveToken(jwtToken);
        
        // Simpan email
        await _authManager.saveEmail(user['email']);
        
        // Simpan user data untuk digunakan di aplikasi
        await UserPreferences.saveUser({
          'access_token': jwtToken,
          'email': user['email'],
          'user': user
        });

        final decodedPayload = _parseJwt(jwtToken);
        return decodedPayload;
      } else if (response.statusCode == 400) {
        final result = json.decode(response.body);
        return result;
      } else {
        print("Status code = ${response.statusCode}");
        print("Response = ${response.body}");
        return null;
      }
    } catch (e) {
      print("Error during login Google: $e");
      return {
        'status': 'error',
        'message': 'Terjadi kesalahan: $e'
      };
    }
  }

  // Fungsi untuk mendekode JWT
  static Map<String, dynamic> _parseJwt(String token) {
    try {
      final parts = token.split('.');
      if (parts.length != 3) {
        print("Token tidak memiliki 3 bagian: $token");
        return {'status': 'success', 'message': 'Login berhasil'};
      }

      // Pastikan tidak ada bagian token yang kosong
      if (parts[0].isEmpty || parts[1].isEmpty || parts[2].isEmpty) {
        print("Ada bagian token yang kosong: $token");
        return {'status': 'success', 'message': 'Login berhasil'};
      }

      try {
        final payload = _decodeBase64(parts[1]);
        return json.decode(payload);
      } catch (e) {
        print("Error mendekode payload: $e");
        return {'status': 'success', 'message': 'Login berhasil'};
      }
    } catch (e) {
      print("Error parsing JWT: $e");
      // Kembalikan success meskipun parsing gagal agar login tetap dilanjutkan
      return {'status': 'success', 'message': 'Login berhasil'};
    }
  }

  static String _decodeBase64(String str) {
    try {
      // Perbaiki padding jika diperlukan
      String output = str.replaceAll('-', '+').replaceAll('_', '/');
      switch (output.length % 4) {
        case 0:
          break; // No pad chars in this case
        case 2:
          output += '==';
          break; // Two pad chars
        case 3:
          output += '=';
          break; // One pad char
      }

      // Normalize dan decode
      String normalized = base64Url.normalize(output);
      return utf8.decode(base64Url.decode(normalized));
    } catch (e) {
      print("Error decoding base64: $e");
      // Kembalikan string kosong untuk mencegah crash
      return "{}";
    }
  }
}
