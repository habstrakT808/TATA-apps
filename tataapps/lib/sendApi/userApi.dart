import 'dart:convert';
import 'package:TATA/SendApi/Server.dart';
import 'package:TATA/SendApi/tokenJWT.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:http/http.dart' as http;

class UserApi {
  static Future<Map<String, dynamic>?> GantiPasswordProfil(
      String passwordOld, String password, String passwordConfirm) async {
    String? token = await TokenJwt.getToken();
    String? email = await TokenJwt.getEmail();
    final response = await http.put(
      Server.urlLaravel("users/profile/profile/password"),
      headers: {
        "Content-Type": "application/json",
        "Authorization": token.toString()
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
    } else {
      print(response.body);
      return null;
    }
  }

  static Future<Map<String, dynamic>?> getProfil(String email) async {
    String? token = await TokenJwt.getToken();
    final response = await http.post(
      Server.urlLaravel("users/profile"),
      headers: {
        "Content-Type": "application/json",
        "Authorization": token.toString()
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
    } else {
      return null;
    }
  }

  static Future<String?> checkEmailAvailability(String email) async {
    try {
      final response = await http.post(
        Server.urlLaravel("mobile/users/check-email"),
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
    try {
      print('Registering user: $email');
    final response = await http.post(
        Server.urlLaravel("mobile/users/register"),
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

      print('Register status code: ${response.statusCode}');
      print('Register response: ${response.body}');

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
        return {'status': 'error', 'message': 'Server error: ${response.statusCode}'};
      }
    } catch (e) {
      print('Exception during registration: $e');
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>?> login(
      String email, String password) async {
    try {
      print('Login attempt for: $email');
    final response = await http.post(
        Server.urlLaravel("mobile/users/login"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({
        "email": email,
        "password": password,
      }),
    );
      
      print('Login status code: ${response.statusCode}');
      print('Login response: ${response.body}');

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
        print('Login success, saving user data: ${result['data']}');
        
        // Simpan data user lengkap (termasuk token) di UserPreferences
        await UserPreferences.saveUser(result['data']);
        
        // Pastikan email juga disimpan di TokenJWT
        String email = result['data']['user']['email'];
        await TokenJwt.saveEmail(email);
        
        print('User data saved to preferences');
        return result['data'];
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else {
        print('Login failed with status: ${response.statusCode}');
        return {'status': 'error', 'message': 'Login failed with status: ${response.statusCode}'};
      }
    } catch (e) {
      print('Exception during login: $e');
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>?> CekEmail(String email) async {
    try {
      print('URL request: ${Server.urlLaravel("mobile/users/check-email")}');
      
    final response = await http.post(
        Server.urlLaravel("mobile/users/check-email"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({"email": email}),
    );

      print('Status code: ${response.statusCode}');
      print('Response body: ${response.body}');

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else {
        return {'status': 'error', 'message': 'Server error: ${response.statusCode}'};
      }
    } catch (e) {
      print('Exception during CekEmail: $e');
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>?> loginGoogle(String email) async {
    try {
      print('Login with Google for: $email');
    final response = await http.post(
        Server.urlLaravel("mobile/users/login-google"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({
        "email": email,
      }),
    );

      print('Google login status code: ${response.statusCode}');
      print('Google login response: ${response.body}');

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
      final jwtToken = result['data'];
      await TokenJwt.saveToken(jwtToken);

      final decodedPayload = _parseJwt(jwtToken);
      return decodedPayload;
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else {
        return {'status': 'error', 'message': 'Server error: ${response.statusCode}'};
      }
    } catch (e) {
      print('Exception during Google login: $e');
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  // Fungsi untuk mendekode JWT
  static Map<String, dynamic> _parseJwt(String token) {
    final parts = token.split('.');
    if (parts.length != 3) {
      throw Exception('Token tidak valid');
    }

    final payload = _decodeBase64(parts[1]);
    return json.decode(payload);
  }

  static String _decodeBase64(String str) {
    String normalized = base64Url.normalize(str);
    return utf8.decode(base64Url.decode(normalized));
  }
}
