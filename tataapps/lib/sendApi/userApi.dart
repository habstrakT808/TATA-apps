import 'dart:convert';
import 'package:TATA/SendApi/Server.dart';
import 'package:TATA/SendApi/tokenJWT.dart';
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

  static Future<Map<String, dynamic>?> login(
      String email, String password) async {
    final response = await http.post(
      Server.urlLaravel("users/login"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({
        "email": email,
        "password": password,
      }),
    );

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
      final jwtToken = result['data']; // ini token e
      await TokenJwt.saveToken(jwtToken);

      final decodedPayload = _parseJwt(jwtToken);
      return decodedPayload;
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else {
      return null;
    }
  }

  static Future<Map<String, dynamic>?> CekEmail(String email) async {
    final response = await http.post(
      Server.urlLaravel("users/CekEmail"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({"email": email}),
    );

    if (response.statusCode == 200) {
      print(response.body.toString());
      final result = json.decode(response.body);
      return result;
    } else if (response.statusCode == 400) {
      final result = json.decode(response.body);
      return result;
    } else {
      return null;
    }
  }

  static Future<Map<String, dynamic>?> loginGoogle(String email) async {
    final response = await http.post(
      Server.urlLaravel("users/login-google"),
      headers: {
        "Content-Type": "application/json",
      },
      body: json.encode({
        "email": email,
      }),
    );

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
      return null;
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
